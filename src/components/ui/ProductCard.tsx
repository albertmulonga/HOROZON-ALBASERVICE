"use client";

import Image from "next/image";
import Link from "next/link";
import { useState } from "react";

interface Product {
  id: number;
  name: string;
  description: string | null;
  price: number;
  originalPrice: number | null;
  image: string | null;
  stock: number;
}

export default function ProductCard({ product }: { product: Product }) {
  const [isAdding, setIsAdding] = useState(false);

  const addToCart = () => {
    setIsAdding(true);
    const cart = JSON.parse(localStorage.getItem("cart") || "[]");
    const existingItem = cart.find((item: any) => item.productId === product.id);
    
    if (existingItem) {
      existingItem.quantity += 1;
    } else {
      cart.push({
        productId: product.id,
        name: product.name,
        price: product.price,
        quantity: 1,
        image: product.image,
      });
    }
    
    localStorage.setItem("cart", JSON.stringify(cart));
    
    // Trigger a custom event to update cart count
    window.dispatchEvent(new Event("cartUpdated"));
    
    setTimeout(() => setIsAdding(false), 500);
  };

  return (
    <div className="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow duration-300">
      <div className="relative h-48 bg-gray-100">
        {product.image ? (
          <Image
            src={product.image}
            alt={product.name}
            fill
            className="object-cover"
          />
        ) : (
          <div className="flex items-center justify-center h-full text-gray-400">
            <svg className="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
        )}
        {product.originalPrice && (
          <span className="absolute top-2 right-2 bg-red-500 text-white text-xs font-bold px-2 py-1 rounded">
            -{Math.round((1 - product.price / product.originalPrice) * 100)}%
          </span>
        )}
      </div>
      
      <div className="p-4">
        <h3 className="text-lg font-semibold text-gray-800 mb-2 line-clamp-2">
          {product.name}
        </h3>
        
        <div className="flex items-center justify-between mb-3">
          <div>
            <span className="text-xl font-bold text-blue-600">
              {product.price.toFixed(2)} $
            </span>
            {product.originalPrice && (
              <span className="ml-2 text-sm text-gray-400 line-through">
                {product.originalPrice.toFixed(2)} $
              </span>
            )}
          </div>
          <span className={`text-xs ${product.stock > 0 ? "text-green-600" : "text-red-500"}`}>
            {product.stock > 0 ? `Stock: ${product.stock}` : "Rupture"}
          </span>
        </div>
        
        <button
          onClick={addToCart}
          disabled={product.stock === 0 || isAdding}
          className={`w-full py-2 px-4 rounded-md font-medium transition-colors ${
            product.stock === 0
              ? "bg-gray-300 text-gray-500 cursor-not-allowed"
              : isAdding
              ? "bg-green-500 text-white"
              : "bg-blue-600 text-white hover:bg-blue-700"
          }`}
        >
          {isAdding ? "Ajouté !" : product.stock === 0 ? "Indisponible" : "Ajouter au panier"}
        </button>
        
        <Link
          href={`/produits/${product.id}`}
          className="block mt-2 text-center text-sm text-blue-600 hover:underline"
        >
          Voir les détails
        </Link>
      </div>
    </div>
  );
}
