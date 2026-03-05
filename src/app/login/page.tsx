"use client";

import { useState, useEffect } from "react";
import { useRouter } from "next/navigation";
import Link from "next/link";
import { loginUser } from "@/lib/actions";

export default function LoginPage() {
  const router = useRouter();
  const [error, setError] = useState("");
  const [loading, setLoading] = useState(false);
  const [captcha, setCaptcha] = useState({ num1: 0, num2: 0, answer: 0 });
  const [userAnswer, setUserAnswer] = useState("");
  const [showPassword, setShowPassword] = useState(false);

  // Generate random math problem - declare function first
  function generateCaptcha() {
    const num1 = Math.floor(Math.random() * 10) + 1;
    const num2 = Math.floor(Math.random() * 10) + 1;
    setCaptcha({ num1, num2, answer: num1 + num2 });
    setUserAnswer("");
  }

  // Generate random math problem
  useEffect(() => {
    generateCaptcha();
  }, []);

  async function handleSubmit(formData: FormData) {
    // Verify captcha
    if (parseInt(userAnswer) !== captcha.answer) {
      setError("Réponse incorrecte. Veuillez résoudre le problème mathématique.");
      generateCaptcha();
      return;
    }

    setLoading(true);
    setError("");

    const result = await loginUser(formData);

    if (result?.error) {
      setError(result.error);
      setLoading(false);
      generateCaptcha();
    }
    // On success, the action will redirect
  }

  return (
    <div className="min-h-screen flex">
      {/* Left Side - Visual */}
      <div className="hidden lg:flex lg:w-1/2 relative overflow-hidden">
        {/* Background with gradient and pattern */}
        <div className="absolute inset-0 bg-gradient-to-br from-blue-900 via-blue-800 to-blue-700">
          {/* Decorative circles */}
          <div className="absolute top-20 left-20 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
          <div className="absolute bottom-20 right-20 w-96 h-96 bg-blue-400/20 rounded-full blur-3xl"></div>
          <div className="absolute top-1/2 left-1/2 w-48 h-48 bg-purple-400/20 rounded-full blur-3xl"></div>
        </div>
        
        {/* Pattern overlay */}
        <div className="absolute inset-0 opacity-10" style={{
          backgroundImage: `url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='1'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E")`,
        }}></div>
        
        {/* Content */}
        <div className="relative z-10 flex flex-col justify-center items-center w-full px-12 text-white">
          <div className="text-center mb-8">
            <div className="w-24 h-24 mx-auto mb-6 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-sm">
              <svg className="w-14 h-14 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1.5} d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
              </svg>
            </div>
            <h2 className="text-4xl font-bold mb-4">HOROZON ALBASERVICE</h2>
            <p className="text-xl text-blue-200">Votre boutique de confiance</p>
          </div>
          
          {/* Feature highlights */}
          <div className="grid grid-cols-2 gap-6 mt-8">
            <div className="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
              <div className="w-12 h-12 mx-auto mb-2 bg-white/20 rounded-full flex items-center justify-center">
                <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
              </div>
              <p className="text-sm font-medium">Large choix</p>
            </div>
            <div className="bg-white/10 backdrop-blur-sm rounded-xl p-4 text-center">
              <div className="w-12 h-12 mx-auto mb-2 bg-white/20 rounded-full flex items-center justify-center">
                <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <p className="text-sm font-medium">Livraison rapide</p>
            </div>
          </div>
        </div>
      </div>

      {/* Right Side - Login Form */}
      <div className="w-full lg:w-1/2 flex items-center justify-center p-8 bg-gradient-to-br from-gray-50 to-gray-100">
        <div className="max-w-md w-full">
          <div className="bg-white rounded-2xl shadow-2xl p-8 border border-gray-100">
            <div className="text-center mb-8">
              <div className="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-600 to-blue-800 rounded-full mb-4 shadow-lg">
                <svg className="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
              </div>
              <h1 className="text-3xl font-bold text-gray-900">Connexion</h1>
              <p className="text-gray-500 mt-2">Connectez-vous à votre compte</p>
            </div>

            {error && (
              <div className="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-6 animate-pulse">
                {error}
              </div>
            )}

            <form action={handleSubmit} className="space-y-5">
              <div>
                <label htmlFor="email" className="block text-sm font-medium text-gray-700 mb-2">
                  Email *
                </label>
                <input
                  type="email"
                  id="email"
                  name="email"
                  required
                  className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                  placeholder="votre@email.com"
                />
              </div>

              <div>
                <label htmlFor="password" className="block text-sm font-medium text-gray-700 mb-2">
                  Mot de passe *
                </label>
                <div className="relative">
                  <input
                    type={showPassword ? "text" : "password"}
                    id="password"
                    name="password"
                    required
                    className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all pr-12"
                    placeholder="••••••••"
                  />
                  <button
                    type="button"
                    onClick={() => setShowPassword(!showPassword)}
                    className="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700"
                  >
                    {showPassword ? (
                      <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                      </svg>
                    ) : (
                      <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                      </svg>
                    )}
                  </button>
                </div>
              </div>

              <div>
                <label className="block text-sm font-medium text-gray-700 mb-2">
                  Vérification *
                </label>
                <div className="flex items-center gap-3">
                  <div className="bg-gradient-to-r from-blue-600 to-blue-800 text-white px-4 py-3 rounded-lg font-bold text-lg">
                    {captcha.num1} + {captcha.num2} = ?
                  </div>
                  <input
                    type="number"
                    value={userAnswer}
                    onChange={(e) => setUserAnswer(e.target.value)}
                    required
                    className="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all"
                    placeholder="Réponse"
                  />
                </div>
                <p className="text-xs text-gray-500 mt-2">Résolvez ce problème mathématique pour continuer</p>
              </div>

              <button
                type="submit"
                disabled={loading}
                className="w-full py-3 px-4 bg-gradient-to-r from-blue-600 to-blue-800 text-white font-semibold rounded-lg hover:from-blue-700 hover:to-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 transition-all duration-200 transform hover:scale-[1.02]"
              >
                {loading ? (
                  <span className="flex items-center justify-center gap-2">
                    <svg className="animate-spin h-5 w-5" viewBox="0 0 24 24">
                      <circle className="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="4" fill="none" />
                      <path className="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z" />
                    </svg>
                    Connexion en cours...
                  </span>
                ) : "Se connecter"}
              </button>
            </form>

            <div className="mt-6 text-center">
              <p className="text-gray-600">
                Pas encore de compte ?{" "}
                <Link href="/register" className="text-blue-600 hover:underline font-medium">
                  Créer un compte
                </Link>
              </p>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
