"use client";

import { useState, useEffect, Suspense } from "react";
import { useRouter } from "next/navigation";
import Link from "next/link";
import { getSetting } from "@/lib/actions";

interface CartItem {
  productId: number;
  name: string;
  price: number;
  quantity: number;
}

function CheckoutContent() {
  const router = useRouter();
  const [cart, setCart] = useState<CartItem[]>([]);
  const [loading, setLoading] = useState(false);
  const [error, setError] = useState("");
  const [paymentPhone, setPaymentPhone] = useState("");
  const [location, setLocation] = useState<{ latitude: number; longitude: number } | null>(null);

  useEffect(() => {
    const savedCart = sessionStorage.getItem("checkout_cart");
    if (savedCart) {
      setCart(JSON.parse(savedCart));
    } else {
      router.push("/panier");
    }

    // Get payment phone
    getSetting("payment_phone").then(phone => {
      if (phone) setPaymentPhone(phone);
    });

    // Get user location
    if (navigator.geolocation) {
      navigator.geolocation.getCurrentPosition(
        (position) => {
          setLocation({
            latitude: position.coords.latitude,
            longitude: position.coords.longitude,
          });
        },
        (err) => {
          console.log("Location error:", err);
        }
      );
    }
  }, [router]);

  const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);

  async function handleSubmit(formData: FormData) {
    setLoading(true);
    setError("");

    const customerName = formData.get("customerName") as string;
    const customerPhone = formData.get("customerPhone") as string;
    const customerAddress = formData.get("customerAddress") as string;
    const customerCity = formData.get("customerCity") as string;
    const transactionNumber = formData.get("transactionNumber") as string;

    if (!customerName || !customerPhone || !customerAddress || !customerCity || !transactionNumber) {
      setError("Veuillez remplir tous les champs");
      setLoading(false);
      return;
    }

    // Create order via API
    try {
      const response = await fetch("/api/orders/create", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          customerName,
          customerPhone,
          customerAddress,
          customerCity,
          latitude: location?.latitude,
          longitude: location?.longitude,
          transactionNumber,
          items: cart,
        }),
      });

      const result = await response.json();

      if (result.success) {
        // Clear cart
        localStorage.removeItem("cart");
        sessionStorage.removeItem("checkout_cart");
        window.dispatchEvent(new Event("cartUpdated"));
        
        // Redirect to order confirmation
        router.push(`/client?order=${result.orderId}&success=true`);
      } else {
        setError(result.error || "Erreur lors de la commande");
        setLoading(false);
      }
    } catch (err) {
      setError("Erreur de connexion");
      setLoading(false);
    }
  }

  if (cart.length === 0) {
    return (
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 text-center">
        <p>Redirection...</p>
      </div>
    );
  }

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <h1 className="text-3xl font-bold mb-8">Finaliser ma commande</h1>

      <div className="flex flex-col lg:flex-row gap-8">
        {/* Checkout Form */}
        <div className="flex-1">
          <form action={handleSubmit} className="bg-white rounded-lg shadow-md p-6">
            <h2 className="text-xl font-semibold mb-6">Informations de livraison</h2>
            
            {error && (
              <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-6">
                {error}
              </div>
            )}

            <div className="space-y-4">
              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Nom complet *
                </label>
                <input
                  type="text"
                  name="customerName"
                  required
                  className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                  placeholder="Votre nom"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Téléphone *
                </label>
                <input
                  type="tel"
                  name="customerPhone"
                  required
                  className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                  placeholder="+243 XXX XXX XXX"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Adresse *
                </label>
                <input
                  type="text"
                  name="customerAddress"
                  required
                  className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                  placeholder="Adresse de livraison"
                />
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Ville *
                </label>
                <input
                  type="text"
                  name="customerCity"
                  required
                  className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                  placeholder="Kindu"
                />
              </div>
            </div>

            <div className="mt-8 pt-6 border-t border-gray-200">
              <h2 className="text-xl font-semibold mb-6">Paiement Mobile Money</h2>
              
              <div className="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p className="text-sm text-gray-700 mb-2">
                  <strong>Numéro de paiement :</strong>{" "}
                  <span className="text-blue-600 font-semibold">{paymentPhone || "+243 000 000 000"}</span>
                </p>
                <p className="text-xs text-gray-500">
                  Envoyez le montant de <strong>{total.toFixed(2)} $</strong> à ce numéro, puis entrez le numéro de transaction ci-dessous.
                </p>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Numéro de transaction *
                </label>
                <input
                  type="text"
                  name="transactionNumber"
                  required
                  className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500"
                  placeholder="Numéro de transaction Mobile Money"
                />
              </div>
            </div>

            <button
              type="submit"
              disabled={loading}
              className="w-full mt-6 py-3 px-4 bg-blue-600 text-white font-semibold rounded-lg hover:bg-blue-700 disabled:opacity-50"
            >
              {loading ? "Traitement en cours..." : "Confirmer la commande"}
            </button>
          </form>
        </div>

        {/* Order Summary */}
        <div className="lg:w-96">
          <div className="bg-white rounded-lg shadow-md p-6 sticky top-24">
            <h2 className="text-xl font-semibold mb-4">Résumé de la commande</h2>
            
            <div className="space-y-3 mb-6">
              {cart.map((item) => (
                <div key={item.productId} className="flex justify-between text-sm">
                  <span className="text-gray-600">
                    {item.name} x {item.quantity}
                  </span>
                  <span className="font-medium">
                    {(item.price * item.quantity).toFixed(2)} $
                  </span>
                </div>
              ))}
            </div>

            <div className="border-t pt-4">
              <div className="flex justify-between text-gray-600 mb-2">
                <span>Sous-total</span>
                <span>{total.toFixed(2)} $</span>
              </div>
              <div className="flex justify-between text-gray-600 mb-4">
                <span>Livraison</span>
                <span>Offerte</span>
              </div>
              <div className="flex justify-between text-xl font-bold">
                <span>Total</span>
                <span className="text-blue-600">{total.toFixed(2)} $</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

export default function CheckoutPage() {
  return (
    <Suspense fallback={<div className="text-center py-16">Chargement...</div>}>
      <CheckoutContent />
    </Suspense>
  );
}
