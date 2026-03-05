"use client";

import Link from "next/link";
import { usePathname, useRouter } from "next/navigation";
import { useState, useEffect, useRef } from "react";

interface User {
  id: number;
  name: string;
  email: string;
  role: string;
  profileImage?: string;
}

export default function Header({ user }: { user: User | null }) {
  const pathname = usePathname();
  const router = useRouter();
  const [isMenuOpen, setIsMenuOpen] = useState(false);
  const [cartCount, setCartCount] = useState(0);
  const [userMenuOpen, setUserMenuOpen] = useState(false);
  const userMenuRef = useRef<HTMLDivElement>(null);

  useEffect(() => {
    // Get cart count from localStorage
    const cart = JSON.parse(localStorage.getItem("cart") || "[]");
    setCartCount(cart.reduce((sum: number, item: any) => sum + item.quantity, 0));
  }, [pathname]);

  // Close user menu when clicking outside
  useEffect(() => {
    function handleClickOutside(event: MouseEvent) {
      if (userMenuRef.current && !userMenuRef.current.contains(event.target as Node)) {
        setUserMenuOpen(false);
      }
    }
    document.addEventListener("mousedown", handleClickOutside);
    return () => document.removeEventListener("mousedown", handleClickOutside);
  }, []);

  const isActive = (path: string) => pathname === path;

  const handleLogout = async () => {
    try {
      await fetch("/api/auth/logout", { method: "POST" });
      window.location.href = "/";
    } catch (error) {
      window.location.href = "/";
    }
  };

  const getDashboardPath = () => {
    if (!user) return "/login";
    switch (user.role) {
      case "admin": return "/admin";
      case "livreur": return "/livreur";
      default: return "/client";
    }
  };

  const getRoleLabel = () => {
    if (!user) return "";
    switch (user.role) {
      case "admin": return "Administrateur";
      case "livreur": return "Livreur";
      default: return "Client";
    }
  };

  return (
    <header className="bg-white shadow-lg sticky top-0 z-50 border-b border-gray-100">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex justify-between items-center h-16">
          {/* Logo */}
          <div className="flex-shrink-0">
            <Link href="/" className="flex items-center gap-2">
              <div className="w-10 h-10 bg-gradient-to-br from-blue-600 to-blue-800 rounded-lg flex items-center justify-center">
                <span className="text-white font-bold text-lg">H</span>
              </div>
              <div className="hidden sm:block">
                <span className="text-xl font-bold text-gray-900">HOROZON</span>
                <span className="text-sm text-blue-600 block -mt-1">ALBASERVICE</span>
              </div>
            </Link>
          </div>

          {/* Desktop Navigation */}
          <nav className="hidden md:flex space-x-1">
            <Link
              href="/"
              className={`px-4 py-2 rounded-lg text-sm font-medium transition-all ${
                isActive("/") 
                  ? "text-blue-600 bg-blue-50" 
                  : "text-gray-700 hover:text-blue-600 hover:bg-gray-50"
              }`}
            >
              Accueil
            </Link>
            <Link
              href="/produits"
              className={`px-4 py-2 rounded-lg text-sm font-medium transition-all ${
                isActive("/produits") 
                  ? "text-blue-600 bg-blue-50" 
                  : "text-gray-700 hover:text-blue-600 hover:bg-gray-50"
              }`}
            >
              Produits
            </Link>
            <Link
              href="/categories"
              className={`px-4 py-2 rounded-lg text-sm font-medium transition-all ${
                isActive("/categories") 
                  ? "text-blue-600 bg-blue-50" 
                  : "text-gray-700 hover:text-blue-600 hover:bg-gray-50"
              }`}
            >
              Catégories
            </Link>
            <Link
              href="/contact"
              className={`px-4 py-2 rounded-lg text-sm font-medium transition-all ${
                isActive("/contact") 
                  ? "text-blue-600 bg-blue-50" 
                  : "text-gray-700 hover:text-blue-600 hover:bg-gray-50"
              }`}
            >
              Contact
            </Link>
          </nav>

          {/* Right side */}
          <div className="hidden md:flex items-center space-x-3">
            {/* Cart */}
            <Link
              href="/panier"
              className="relative p-2 text-gray-700 hover:text-blue-600 transition-colors rounded-lg hover:bg-gray-50"
            >
              <svg className="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
              </svg>
              {cartCount > 0 && (
                <span className="absolute -top-1 -right-1 bg-red-500 text-white text-xs font-bold rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
                  {cartCount}
                </span>
              )}
            </Link>

            {user ? (
              <div className="relative" ref={userMenuRef}>
                <button
                  onClick={() => setUserMenuOpen(!userMenuOpen)}
                  className="flex items-center space-x-2 px-3 py-2 rounded-lg hover:bg-gray-50 transition-colors"
                >
                  <div className="w-9 h-9 rounded-full bg-gradient-to-br from-blue-500 to-blue-700 flex items-center justify-center text-white font-semibold shadow-md">
                    {user.name.charAt(0).toUpperCase()}
                  </div>
                  <div className="text-left hidden lg:block">
                    <p className="text-sm font-medium text-gray-900">{user.name}</p>
                    <p className="text-xs text-blue-600">{getRoleLabel()}</p>
                  </div>
                  <svg 
                    className={`w-4 h-4 text-gray-500 transition-transform ${userMenuOpen ? 'rotate-180' : ''}`} 
                    fill="none" 
                    stroke="currentColor" 
                    viewBox="0 0 24 24"
                  >
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M19 9l-7 7-7-7" />
                  </svg>
                </button>

                {/* Dropdown Menu */}
                {userMenuOpen && (
                  <div className="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 py-2 animate-in fade-in slide-in-from-top-2 duration-200">
                    <div className="px-4 py-3 border-b border-gray-100">
                      <p className="text-sm font-medium text-gray-900">{user.name}</p>
                      <p className="text-xs text-gray-500">{user.email}</p>
                      <span className="inline-block mt-1 px-2 py-0.5 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">
                        {getRoleLabel()}
                      </span>
                    </div>
                    
                    <Link
                      href={getDashboardPath()}
                      className="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors"
                      onClick={() => setUserMenuOpen(false)}
                    >
                      <svg className="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                      </svg>
                      Tableau de bord
                    </Link>

                    {user.role === "client" && (
                      <Link
                        href="/client"
                        className="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors"
                        onClick={() => setUserMenuOpen(false)}
                      >
                        <svg className="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                        </svg>
                        Mes commandes
                      </Link>
                    )}

                    {user.role === "admin" && (
                      <>
                        <Link
                          href="/admin/produits"
                          className="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors"
                          onClick={() => setUserMenuOpen(false)}
                        >
                          <svg className="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                          </svg>
                          Gérer les produits
                        </Link>
                        <Link
                          href="/admin/commandes"
                          className="flex items-center px-4 py-2.5 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition-colors"
                          onClick={() => setUserMenuOpen(false)}
                        >
                          <svg className="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                          </svg>
                          Gérer les commandes
                        </Link>
                      </>
                    )}

                    <div className="border-t border-gray-100 mt-2 pt-2">
                      <button
                        onClick={handleLogout}
                        className="flex items-center w-full px-4 py-2.5 text-sm text-red-600 hover:bg-red-50 transition-colors"
                      >
                        <svg className="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                        Déconnexion
                      </button>
                    </div>
                  </div>
                )}
              </div>
            ) : (
              <div className="flex items-center space-x-3">
                <Link
                  href="/login"
                  className="px-4 py-2 text-sm font-medium text-blue-600 border-2 border-blue-600 rounded-lg hover:bg-blue-50 transition-colors"
                >
                  Connexion
                </Link>
                <Link
                  href="/register"
                  className="px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-blue-800 rounded-lg hover:from-blue-700 hover:to-blue-900 transition-all shadow-md hover:shadow-lg"
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
              className="p-2 rounded-lg text-gray-700 hover:bg-gray-100"
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
            <div className="flex flex-col space-y-1">
              <Link
                href="/"
                className={`px-4 py-3 rounded-lg text-sm font-medium ${
                  isActive("/") ? "text-blue-600 bg-blue-50" : "text-gray-700"
                }`}
                onClick={() => setIsMenuOpen(false)}
              >
                Accueil
              </Link>
              <Link
                href="/produits"
                className={`px-4 py-3 rounded-lg text-sm font-medium ${
                  isActive("/produits") ? "text-blue-600 bg-blue-50" : "text-gray-700"
                }`}
                onClick={() => setIsMenuOpen(false)}
              >
                Produits
              </Link>
              <Link
                href="/categories"
                className={`px-4 py-3 rounded-lg text-sm font-medium ${
                  isActive("/categories") ? "text-blue-600 bg-blue-50" : "text-gray-700"
                }`}
                onClick={() => setIsMenuOpen(false)}
              >
                Catégories
              </Link>
              <Link
                href="/contact"
                className={`px-4 py-3 rounded-lg text-sm font-medium ${
                  isActive("/contact") ? "text-blue-600 bg-blue-50" : "text-gray-700"
                }`}
                onClick={() => setIsMenuOpen(false)}
              >
                Contact
              </Link>
              <Link
                href="/panier"
                className={`px-4 py-3 rounded-lg text-sm font-medium ${
                  isActive("/panier") ? "text-blue-600 bg-blue-50" : "text-gray-700"
                }`}
                onClick={() => setIsMenuOpen(false)}
              >
                Panier ({cartCount})
              </Link>
              
              {user ? (
                <>
                  <div className="border-t border-gray-200 pt-2 mt-2">
                    <p className="px-4 py-2 text-xs text-gray-500 font-medium">MON COMPTE</p>
                    <Link
                      href={getDashboardPath()}
                      className="px-4 py-3 rounded-lg text-sm font-medium text-gray-700"
                      onClick={() => setIsMenuOpen(false)}
                    >
                      Tableau de bord
                    </Link>
                    <button
                      onClick={() => {
                        handleLogout();
                        setIsMenuOpen(false);
                      }}
                      className="w-full text-left px-4 py-3 rounded-lg text-sm font-medium text-red-600"
                    >
                      Déconnexion
                    </button>
                  </div>
                </>
              ) : (
                <div className="border-t border-gray-200 pt-2 mt-2">
                  <p className="px-4 py-2 text-xs text-gray-500 font-medium">AUTHENTIFICATION</p>
                  <Link
                    href="/login"
                    className="block px-4 py-3 text-sm font-medium text-blue-600"
                    onClick={() => setIsMenuOpen(false)}
                  >
                    Connexion
                  </Link>
                  <Link
                    href="/register"
                    className="block px-4 py-3 text-sm font-medium text-white bg-blue-600 rounded-lg mx-4 text-center"
                    onClick={() => setIsMenuOpen(false)}
                  >
                    Créer un compte
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
