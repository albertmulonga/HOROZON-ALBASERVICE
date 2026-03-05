import { redirect } from "next/navigation";
import Link from "next/link";
import { getCurrentUser } from "@/lib/auth";
import { getOrdersByUser, getOrderById } from "@/lib/orders";

export default async function ClientDashboard({
  searchParams,
}: {
  searchParams: Promise<{ order?: string; success?: string }>;
}) {
  const user = await getCurrentUser();
  
  if (!user) {
    redirect("/login");
  }

  if (user.role !== "client") {
    redirect("/admin");
  }

  const params = await searchParams;
  const orders = await getOrdersByUser(user.id);

  const statusColors: Record<string, string> = {
    en_attente: "bg-yellow-100 text-yellow-800",
    paye: "bg-blue-100 text-blue-800",
    en_preparation: "bg-purple-100 text-purple-800",
    en_livraison: "bg-orange-100 text-orange-800",
    livre: "bg-green-100 text-green-800",
    annule: "bg-red-100 text-red-800",
  };

  const statusLabels: Record<string, string> = {
    en_attente: "En attente de paiement",
    paye: "Payé - En préparation",
    en_preparation: "En préparation",
    en_livraison: "En livraison",
    livre: "Livré",
    annule: "Annulé",
  };

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      {/* Success Message */}
      {params.success === "true" && (
        <div className="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded mb-6">
          <div className="flex items-center">
            <svg className="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
              <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
            </svg>
            <span>Commande créée avec succès ! Veuillez attendre la validation du paiement.</span>
          </div>
        </div>
      )}

      {/* Header */}
      <div className="flex justify-between items-center mb-8">
        <div>
          <h1 className="text-3xl font-bold text-gray-900">Mon Compte</h1>
          <p className="text-gray-600">Bienvenue, {user.name}</p>
        </div>
        <Link
          href="/produits"
          className="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
        >
         Continuer mes achats
        </Link>
      </div>

      {/* Profile Card */}
      <div className="bg-white rounded-lg shadow-md p-6 mb-8">
        <h2 className="text-xl font-semibold mb-4">Mes informations</h2>
        <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <p className="text-sm text-gray-500">Nom</p>
            <p className="font-medium">{user.name}</p>
          </div>
          <div>
            <p className="text-sm text-gray-500">Email</p>
            <p className="font-medium">{user.email}</p>
          </div>
          <div>
            <p className="text-sm text-gray-500">Téléphone</p>
            <p className="font-medium">{user.phone || "Non défini"}</p>
          </div>
          <div>
            <p className="text-sm text-gray-500">Adresse</p>
            <p className="font-medium">{user.address || "Non définie"}</p>
          </div>
        </div>
      </div>

      {/* Orders */}
      <div className="bg-white rounded-lg shadow-md overflow-hidden">
        <div className="p-6 border-b border-gray-200">
          <h2 className="text-xl font-semibold">Mes commandes</h2>
        </div>

        {orders.length > 0 ? (
          <div className="divide-y divide-gray-200">
            {orders.map((order) => (
              <div key={order.id} className="p-6">
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                  <div>
                    <div className="flex items-center gap-4 mb-2">
                      <span className="text-lg font-semibold">Commande #{order.id}</span>
                      <span className={`px-3 py-1 rounded-full text-sm font-medium ${statusColors[order.status]}`}>
                        {statusLabels[order.status]}
                      </span>
                    </div>
                    <p className="text-gray-600">
                      {new Date(order.createdAt).toLocaleDateString("fr-FR", {
                        day: "numeric",
                        month: "long",
                        year: "numeric",
                        hour: "2-digit",
                        minute: "2-digit",
                      })}
                    </p>
                    <p className="text-gray-600 mt-1">
                      {order.customerAddress}, {order.customerCity}
                    </p>
                  </div>
                  
                  <div className="text-right">
                    <p className="text-2xl font-bold text-blue-600">
                      {order.totalAmount.toFixed(2)} $
                    </p>
                    
                    {order.status === "en_livraison" && order.customerLatitude && order.customerLongitude && (
                      <Link
                        href={`/client/track?order=${order.id}`}
                        className="inline-block mt-2 px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600"
                      >
                        Suivre ma livraison
                      </Link>
                    )}
                    
                    {order.status === "paye" || order.status === "en_preparation" ? (
                      <p className="text-sm text-gray-500 mt-2">
                        Votre commande est en cours de traitement
                      </p>
                    ) : null}
                  </div>
                </div>
              </div>
            ))}
          </div>
        ) : (
          <div className="p-12 text-center">
            <svg className="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
            </svg>
            <p className="text-gray-500 mb-4">Vous n'avez pas encore de commande</p>
            <Link
              href="/produits"
              className="inline-block px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700"
            >
              Découvrir nos produits
            </Link>
          </div>
        )}
      </div>
    </div>
  );
}
