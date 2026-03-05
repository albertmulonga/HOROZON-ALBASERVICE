import { redirect } from "next/navigation";
import Link from "next/link";
import { getCurrentUser } from "@/lib/auth";
import { getOrdersByUser } from "@/lib/orders";

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

  // Calculate stats
  const totalOrders = orders.length;
  const deliveredOrders = orders.filter(o => o.status === "livre").length;
  const totalSpent = orders
    .filter(o => o.status === "livre" || o.status === "en_livraison" || o.status === "en_preparation" || o.status === "paye")
    .reduce((sum, o) => sum + o.totalAmount, 0);

  const statusColors: Record<string, string> = {
    en_attente: "bg-yellow-100 text-yellow-800 border-yellow-200",
    paye: "bg-blue-100 text-blue-800 border-blue-200",
    en_preparation: "bg-purple-100 text-purple-800 border-purple-200",
    en_livraison: "bg-orange-100 text-orange-800 border-orange-200",
    livre: "bg-green-100 text-green-800 border-green-200",
    annule: "bg-red-100 text-red-800 border-red-200",
  };

  const statusLabels: Record<string, string> = {
    en_attente: "En attente de paiement",
    paye: "Payé - En préparation",
    en_preparation: "En préparation",
    en_livraison: "En livraison",
    livre: "Livré",
    annule: "Annulé",
  };

  const statusIcons: Record<string, React.ReactNode> = {
    en_attente: (
      <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
    ),
    paye: (
      <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
      </svg>
    ),
    en_preparation: (
      <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
      </svg>
    ),
    en_livraison: (
      <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
      </svg>
    ),
    livre: (
      <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
      </svg>
    ),
    annule: (
      <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M6 18L18 6M6 6l12 12" />
      </svg>
    ),
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
      {/* Header */}
      <div className="bg-white shadow-sm border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
              <h1 className="text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                Mon Compte
              </h1>
              <p className="text-gray-600 mt-1">Bienvenue, <span className="font-semibold text-blue-600">{user.name}</span></p>
            </div>
            <div className="flex items-center gap-3">
              <Link
                href="/produits"
                className="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all shadow-md hover:shadow-lg flex items-center gap-2"
              >
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                </svg>
                Continue mes achats
              </Link>
            </div>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Success Message */}
        {params.success === "true" && (
          <div className="bg-green-50 border border-green-200 text-green-700 px-6 py-4 rounded-xl mb-6 flex items-center gap-3 shadow-sm">
            <div className="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
              <svg className="w-6 h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
              </svg>
            </div>
            <div>
              <p className="font-semibold">Commande créée avec succès !</p>
              <p className="text-sm">Veuillez attendre la validation du paiement.</p>
            </div>
          </div>
        )}

        {/* Stats Cards */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div className="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
            <div className="flex items-center justify-between mb-4">
              <div className="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center shadow-lg">
                <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
              </div>
            </div>
            <p className="text-sm text-gray-500 font-medium">Total Commandes</p>
            <p className="text-3xl font-bold text-gray-900 mt-1">{totalOrders}</p>
          </div>

          <div className="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
            <div className="flex items-center justify-between mb-4">
              <div className="w-12 h-12 bg-gradient-to-br from-green-500 to-green-700 rounded-xl flex items-center justify-center shadow-lg">
                <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                </svg>
              </div>
            </div>
            <p className="text-sm text-gray-500 font-medium">Livraisons réussies</p>
            <p className="text-3xl font-bold text-gray-900 mt-1">{deliveredOrders}</p>
          </div>

          <div className="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
            <div className="flex items-center justify-between mb-4">
              <div className="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl flex items-center justify-center shadow-lg">
                <svg className="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
            </div>
            <p className="text-sm text-gray-500 font-medium">Total dépensé</p>
            <p className="text-3xl font-bold text-gray-900 mt-1">{totalSpent.toFixed(2)} $</p>
          </div>
        </div>

        {/* Profile Card */}
        <div className="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
          <h2 className="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
            <svg className="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
            </svg>
            Mes informations
          </h2>
          <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <div className="p-4 bg-gray-50 rounded-xl">
              <p className="text-xs text-gray-500 font-medium uppercase tracking-wide">Nom complet</p>
              <p className="font-semibold text-gray-900 mt-1">{user.name}</p>
            </div>
            <div className="p-4 bg-gray-50 rounded-xl">
              <p className="text-xs text-gray-500 font-medium uppercase tracking-wide">Email</p>
              <p className="font-semibold text-gray-900 mt-1">{user.email}</p>
            </div>
            <div className="p-4 bg-gray-50 rounded-xl">
              <p className="text-xs text-gray-500 font-medium uppercase tracking-wide">Téléphone</p>
              <p className="font-semibold text-gray-900 mt-1">{user.phone || "Non défini"}</p>
            </div>
            <div className="p-4 bg-gray-50 rounded-xl">
              <p className="text-xs text-gray-500 font-medium uppercase tracking-wide">Adresse</p>
              <p className="font-semibold text-gray-900 mt-1">{user.address || "Non définie"}</p>
            </div>
          </div>
        </div>

        {/* Orders */}
        <div className="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
          <div className="p-6 border-b border-gray-100">
            <h2 className="text-lg font-semibold text-gray-900 flex items-center gap-2">
              <svg className="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
              </svg>
              Mes commandes
            </h2>
          </div>

          {orders.length > 0 ? (
            <div className="divide-y divide-gray-100">
              {orders.map((order) => (
                <div key={order.id} className="p-6 hover:bg-gray-50 transition-colors">
                  <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div className="flex items-start gap-4">
                      <div className={`w-12 h-12 rounded-xl flex items-center justify-center ${
                        order.status === 'livre' ? 'bg-green-100' :
                        order.status === 'en_livraison' ? 'bg-orange-100' :
                        order.status === 'en_preparation' ? 'bg-purple-100' :
                        order.status === 'paye' ? 'bg-blue-100' :
                        order.status === 'annule' ? 'bg-red-100' : 'bg-yellow-100'
                      }`}>
                        <span className={
                          order.status === 'livre' ? 'text-green-600' :
                          order.status === 'en_livraison' ? 'text-orange-600' :
                          order.status === 'en_preparation' ? 'text-purple-600' :
                          order.status === 'paye' ? 'text-blue-600' :
                          order.status === 'annule' ? 'text-red-600' : 'text-yellow-600'
                        }>
                          {statusIcons[order.status]}
                        </span>
                      </div>
                      <div>
                        <div className="flex items-center gap-3 mb-1">
                          <span className="text-lg font-bold text-gray-900">Commande #{order.id}</span>
                          <span className={`px-3 py-1 rounded-full text-xs font-medium border ${statusColors[order.status]}`}>
                            {statusLabels[order.status]}
                          </span>
                        </div>
                        <p className="text-gray-600 text-sm">
                          {order.createdAt ? new Date(order.createdAt).toLocaleDateString("fr-FR", {
                            day: "numeric",
                            month: "long",
                            year: "numeric",
                            hour: "2-digit",
                            minute: "2-digit",
                          }) : "Date non disponible"}
                        </p>
                        <p className="text-gray-600 text-sm mt-1 flex items-center gap-1">
                          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                          </svg>
                          {order.customerAddress}, {order.customerCity}
                        </p>
                      </div>
                    </div>
                    
                    <div className="text-right lg:min-w-[150px]">
                      <p className="text-2xl font-bold text-blue-600">{order.totalAmount.toFixed(2)} $</p>
                      
                      {order.status === "en_livraison" && order.customerLatitude && order.customerLongitude && (
                        <a
                          href={`https://www.google.com/maps/dir/?api=1&destination=${order.customerLatitude},${order.customerLongitude}`}
                          target="_blank"
                          rel="noopener noreferrer"
                          className="inline-flex items-center gap-2 mt-3 px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600 transition-colors"
                        >
                          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                          </svg>
                          Suivre
                        </a>
                      )}
                      
                      {(order.status === "paye" || order.status === "en_preparation") && (
                        <p className="text-sm text-gray-500 mt-2">
                          En cours de traitement
                        </p>
                      )}
                    </div>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div className="p-12 text-center">
              <div className="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg className="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
              </div>
              <p className="text-gray-500 mb-4 text-lg">Vous n'avez pas encore de commande</p>
              <Link
                href="/produits"
                className="inline-flex items-center gap-2 px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all shadow-md"
              >
                Découvrir nos produits
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 8l4 4m0 0l-4 4m4-4H3" />
                </svg>
              </Link>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
