import { redirect } from "next/navigation";
import { getCurrentUser } from "@/lib/auth";
import { getDeliveryPersonOrders, updateOrderStatus, startDelivery, completeDelivery } from "@/lib/orders";

export default async function LivreurDashboard() {
  const user = await getCurrentUser();
  
  if (!user || user.role !== "livreur") {
    redirect("/login");
  }

  const orders = await getDeliveryPersonOrders(user.id);

  const statusColors: Record<string, string> = {
    en_attente: "bg-yellow-100 text-yellow-800 border-yellow-200",
    paye: "bg-blue-100 text-blue-800 border-blue-200",
    en_preparation: "bg-purple-100 text-purple-800 border-purple-200",
    en_livraison: "bg-orange-100 text-orange-800 border-orange-200",
    livre: "bg-green-100 text-green-800 border-green-200",
    annule: "bg-red-100 text-red-800 border-red-200",
  };

  const statusLabels: Record<string, string> = {
    en_attente: "En attente",
    paye: "Payé - À récupérer",
    en_preparation: "En préparation",
    en_livraison: "En livraison",
    livre: "Livré",
    annule: "Annulé",
  };

  const activeOrders = orders.filter(o => o.status === "paye" || o.status === "en_preparation" || o.status === "en_livraison");
  const completedOrders = orders.filter(o => o.status === "livre");

  return (
    <div className="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
      {/* Header */}
      <div className="bg-white shadow-sm border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
              <h1 className="text-3xl font-bold bg-gradient-to-r from-orange-600 to-orange-800 bg-clip-text text-transparent">
                Tableau de bord Livreur
              </h1>
              <p className="text-gray-600 mt-1">Bienvenue, <span className="font-semibold text-orange-600">{user.name}</span></p>
            </div>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Stats Cards */}
        <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
          <div className="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
            <div className="flex items-center justify-between mb-4">
              <div className="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-700 rounded-xl flex items-center justify-center shadow-lg">
                <svg className="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                </svg>
              </div>
              <span className="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-1 rounded-full">
                À livrer
              </span>
            </div>
            <p className="text-sm text-gray-500 font-medium">Commandes à livrer</p>
            <p className="text-3xl font-bold text-gray-900 mt-1">{activeOrders.length}</p>
          </div>

          <div className="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
            <div className="flex items-center justify-between mb-4">
              <div className="w-14 h-14 bg-gradient-to-br from-green-500 to-green-700 rounded-xl flex items-center justify-center shadow-lg">
                <svg className="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                </svg>
              </div>
            </div>
            <p className="text-sm text-gray-500 font-medium">Livraisons terminées</p>
            <p className="text-3xl font-bold text-gray-900 mt-1">{completedOrders.length}</p>
          </div>

          <div className="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow">
            <div className="flex items-center justify-between mb-4">
              <div className="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center shadow-lg">
                <svg className="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                </svg>
              </div>
            </div>
            <p className="text-sm text-gray-500 font-medium">Total</p>
            <p className="text-3xl font-bold text-gray-900 mt-1">{orders.length}</p>
          </div>
        </div>

        {/* Orders to Deliver */}
        <div className="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-8">
          <div className="p-6 border-b border-gray-100">
            <h2 className="text-lg font-semibold text-gray-900 flex items-center gap-2">
              <svg className="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
              </svg>
              Commandes à livrer
            </h2>
          </div>

          {activeOrders.length > 0 ? (
            <div className="divide-y divide-gray-100">
              {activeOrders.map((order) => (
                <div key={order.id} className="p-6 hover:bg-gray-50 transition-colors">
                  <div className="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                    <div className="flex items-start gap-4">
                      <div className={`w-12 h-12 rounded-xl flex items-center justify-center ${
                        order.status === 'en_livraison' ? 'bg-orange-100' :
                        order.status === 'en_preparation' ? 'bg-purple-100' : 'bg-blue-100'
                      }`}>
                        {order.status === 'en_livraison' ? (
                          <svg className="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                          </svg>
                        ) : order.status === 'en_preparation' ? (
                          <svg className="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4" />
                          </svg>
                        ) : (
                          <svg className="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                          </svg>
                        )}
                      </div>
                      <div>
                        <div className="flex items-center gap-3 mb-1">
                          <span className="text-lg font-bold text-gray-900">Commande #{order.id}</span>
                          <span className={`px-3 py-1 rounded-full text-xs font-medium border ${statusColors[order.status]}`}>
                            {statusLabels[order.status]}
                          </span>
                        </div>
                        <div className="flex items-center gap-4 text-gray-600 text-sm">
                          <span className="flex items-center gap-1">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            {order.customerName}
                          </span>
                          <span className="flex items-center gap-1">
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                            {order.customerPhone}
                          </span>
                        </div>
                        <p className="text-gray-600 text-sm mt-1 flex items-center gap-1">
                          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                          </svg>
                          {order.customerAddress}, {order.customerCity}
                        </p>
                        {order.customerLatitude && order.customerLongitude && (
                          <a
                            href={`https://www.google.com/maps/dir/?api=1&destination=${order.customerLatitude},${order.customerLongitude}`}
                            target="_blank"
                            rel="noopener noreferrer"
                            className="inline-flex items-center gap-1 mt-2 text-blue-600 hover:text-blue-700 text-sm font-medium"
                          >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            Voir sur Google Maps
                          </a>
                        )}
                      </div>
                    </div>
                    
                    <div className="text-right lg:min-w-[150px]">
                      <p className="text-2xl font-bold text-gray-900">{order.totalAmount.toFixed(2)} $</p>
                      
                      <form action={async () => {
                        "use server";
                        if (order.status === "en_livraison") {
                          await completeDelivery(order.id);
                        } else if (order.status === "paye" || order.status === "en_preparation") {
                          await startDelivery(order.id);
                        }
                      }}>
                        {order.status === "en_livraison" ? (
                          <button
                            type="submit"
                            className="mt-3 px-4 py-2 bg-gradient-to-r from-green-500 to-green-600 text-white rounded-lg hover:from-green-600 hover:to-green-700 transition-all shadow-md flex items-center gap-2"
                          >
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                            </svg>
                            Marquer livré
                          </button>
                        ) : (order.status === "paye" || order.status === "en_preparation") && (
                          <button
                            type="submit"
                            className="mt-3 px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 text-white rounded-lg hover:from-orange-600 hover:to-orange-700 transition-all shadow-md flex items-center gap-2"
                          >
                            <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                            </svg>
                            Commencer livraison
                          </button>
                        )}
                      </form>
                    </div>
                  </div>
                </div>
              ))}
            </div>
          ) : (
            <div className="p-12 text-center">
              <div className="w-20 h-20 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <svg className="w-10 h-10 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                </svg>
              </div>
              <p className="text-gray-500 text-lg">Aucune commande à livrer pour le moment</p>
              <p className="text-gray-400 text-sm mt-2">Les nouvelles commandes apparaîtront ici</p>
            </div>
          )}
        </div>

        {/* Completed Deliveries */}
        {completedOrders.length > 0 && (
          <div className="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div className="p-6 border-b border-gray-100">
              <h2 className="text-lg font-semibold text-gray-900 flex items-center gap-2">
                <svg className="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                </svg>
                Livraisons terminées
              </h2>
            </div>
            <div className="divide-y divide-gray-100">
              {completedOrders.map((order) => (
                <div key={order.id} className="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                  <div className="flex items-center gap-4">
                    <div className="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center">
                      <svg className="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M5 13l4 4L19 7" />
                      </svg>
                    </div>
                    <div>
                      <p className="font-semibold text-gray-900">Commande #{order.id}</p>
                      <p className="text-sm text-gray-500">{order.customerName} - {order.customerCity}</p>
                    </div>
                  </div>
                  <div className="text-right">
                    <span className="inline-block px-3 py-1 rounded-full text-xs font-medium border bg-green-100 text-green-800 border-green-200">
                      Livré
                    </span>
                    <p className="text-sm font-bold text-gray-900 mt-1">{order.totalAmount.toFixed(2)} $</p>
                  </div>
                </div>
              ))}
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
