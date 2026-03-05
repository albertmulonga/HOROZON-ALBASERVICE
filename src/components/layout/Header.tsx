"use client";

import Link from "next/link";
import { usePathname } from "next/navigation";
import { useState, useEffect } from "react";

interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  profileImage?: string;
}

export default function Header({ user }: { user: User | null }) {
  const pathname = usePathname();
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [cartCount, setCartCount] = useState(0);

  useEffect(() => {
    // Get cart count from localStorage
    const cart = JSON.parse(localStorage.getItem("cart") || "[]");
    setCartCount(cart.reduce((sum: number, item: any) => sum + item.quantity, 0));
  }, [pathname]);

  const isActive = (path: string) => pathname === path;

  return (
    <header className="bg-white shadow-md sticky top-0 z-50">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between items-center h-16">
          {/* Logo */}
          <div className="flex-shrink-0">
            <Link href="/" className="text-2xl font-bold text-blue-900">
              HIRIZON DE KINDU
            </Link>
          </div>

          {/* Desktop Navigation */}
          <nav className="hidden md:flex space-x-8">
            <Link
              href="/"
              className={`px-3 py-2 rounded-md text-sm font-medium ${
                isActive("/") ? "text-blue-600 bg-blue-50" : "text-gray-700 hover:text-blue-600"
              }`}
            >
              Accueil
            </Link>
            <Link
              href="/produits"
              className={`px-3 py-2 rounded-md text-sm font-medium ${
                isActive("/produits") ? "text-blue-600 bg-blue-50" : "text-gray-700 hover:text-blue-600"
              }`}
            >
              Produits
            </Link>
            <Link
              href="/categories"
              className={`px-3 py-2 rounded-md text-sm font-medium ${
                isActive("/categories") ? "text-blue-600 bg-blue-50" : "text-gray-700 hover:text-blue-600"
              }`}
            >
              Catégories
            </Link>
            <Link
              href="/contact"
              className={`px-3 py-2 rounded-md text-sm font-medium ${
                isActive("/contact") ? "text-blue-600 bg-blue-50" : "text-gray-700 hover:text-blue-600"
              }`}
            >
              Contact
            </Link>
          </nav>

          {/* Right side */}
          <div className="hidden md:flex items-center space-x-4">
            {/* Cart */}
            <Link
              href="/panier"
              className="relative p-2 text-gray-700 hover:text-blue-600"
            >
              <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              {cartCount > 0 && (
                <span className="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center">
                  {cartCount}
                </span>
              )}
            </Link>

            {user ? (
              <div className="flex items-center space-x-4">
                <Link
                  href={user.role === "admin" ? "/admin" : user.role === "livreur" ? "/livreur" : "/client"}
                  className="flex items-center space-x-2 text-gray-700 hover:text-blue-600"
                >
                  <div className="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white">
                    {user.name.charAt(0).toUpperCase()}
                  </div>
                  <span className="text-sm font-medium">{user.name}</span>
                </Link>
              </div>
            ) : (
              <div className="flex items-center space-x-4">
                <Link
                  href="/login"
                  className="px-4 py-2 text-sm font-medium text-blue-600 border border-blue-600 rounded-md hover:bg-blue-50"
                >
                  Connexion
                </Link>
                <Link
                  href="/register"
                  className="px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700"
                >
                  Créer un compte
                </Link>
              </div>
            )}
          </div>

          {/* Mobile menu button */}
          <div className="md:hidden">
            <button
              onClick={() => setIsMenuOpen(!isMenuOpen)}
              className="p-2 rounded-md text-gray-700 hover:text-blue-600"
            >
              <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                {isMenuOpen ? (
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
                ) : (
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 12h16M4 18h16" />
                )}
              </svg>
            </button>
          </div>
        </div>

        {/* Mobile menu */}
        {isMenuOpen && (
          <div className="md:hidden pb-4">
            <div className="flex flex-col space-y-2">
              <Link
                href="/"
                className={`px-3 py-2 rounded-md text-sm font-medium ${
                  isActive("/") ? "text-blue-600 bg-blue-50" : "text-gray-700"
                }`}
                onClick={() => setIsMenuOpen(false)}
              >
                Accueil
              </Link>
              <Link
                href="/produits"
                className={`px-3 py-2 rounded-md text-sm font-medium ${
                  isActive("/produits") ? "text-blue-600 bg-blue-50" : "text-gray-700"
                }`}
                onClick={() => setIsMenuOpen(false)}
              >
                Produits
              </Link>
              <Link
                href="/categories"
                className={`px-3 py-2 rounded-md text-sm font-medium ${
                  isActive("/categories") ? "text-blue-600 bg-blue-50" : "text-gray-700"
                }`}
                onClick={() => setIsMenuOpen(false)}
              >
                Catégories
              </Link>
              <Link
                href="/contact"
                className={`px-3 py-2 rounded-md text-sm font-medium ${
                  isActive("/contact") ? "text-blue-600 bg-blue-50" : "text-gray-700"
                }`}
                onClick={() => setIsMenuOpen(false)}
              >
                Contact
              </Link>
              <Link
                href="/panier"
                className={`px-3 py-2 rounded-md text-sm font-medium ${
                  isActive("/panier") ? "text-blue-600 bg-blue-50" : "text-gray-700"
                }`}
                onClick={() => setIsMenuOpen(false)}
              >
                Panier ({cartCount})
              </Link>
              {user ? (
                <Link
                  href={user.role === "admin" ? "/admin" : user.role === "livreur" ? "/livreur" : "/client"}
                  className="px-3 py-2 rounded-md text-sm font-medium text-gray-700"
                  onClick={() => setIsMenuOpen(false)}
                >
                  Mon Compte
                </Link>
              ) : (
                <div className="flex space-x-2 pt-2">
                  <Link
                    href="/login"
                    className="flex-1 px-3 py-2 text-center text-sm font-medium text-blue-600 border border-blue-600 rounded-md"
                    onClick={() => setIsMenuOpen(false)}
                  >
                    Connexion
                  </Link>
                  <Link
                    href="/register"
                    className="flex-1 px-3 py-2 text-center text-sm font-medium text-white bg-blue"
                    onClick={() => setIsMenuOpen(false)}
                  >
                    Cré-600 rounded-mder un compte
                  </Link>
                </div>
              )}
            </div>
          </div>
        )}
      </div>
    </header>
  );
}
