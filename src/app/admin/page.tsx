import { redirect } from "next/navigation";
import Link from "next/link";
import { getCurrentUser } from "@/lib/auth";
import { getAllOrders, getOrderStats, getUsersCount } from "@/lib/orders";
import { getProducts, getCategories as getCats } from "@/lib/products";

export default async function AdminDashboard() {
  const user = await getCurrentUser();
  
  if (!user || user.role !== "admin") {
    redirect("/login");
  }

  const stats = await getOrderStats();
  const usersStats = await getUsersCount();
  const recentOrders = (await getAllOrders()).slice(0, 8);
  const products = await getProducts();
  const categories = await getCats();

  // Calculate revenue comparison (mock data for demonstration)
  const currentMonthRevenue = stats.totalSales;
  const previousMonthRevenue = stats.totalSales * 0.75; // Mock previous month
  const revenueChange = ((currentMonthRevenue - previousMonthRevenue) / previousMonthRevenue) * 100;

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
    paye: "Payé",
    en_preparation: "En préparation",
    en_livraison: "En livraison",
    livre: "Livré",
    annule: "Annulé",
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100">
      {/* Header */}
      <div className="bg-white shadow-sm border-b border-gray-200">
        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
          <div className="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div>
              <h1 className="text-3xl font-bold bg-gradient-to-r from-blue-600 to-blue-800 bg-clip-text text-transparent">
                Tableau de bord Admin
              </h1>
              <p className="text-gray-600 mt-1">Bienvenue, <span className="font-semibold text-blue-600">{user.name}</span></p>
            </div>
            <div className="flex items-center gap-3">
              <Link
                href="/admin/produits"
                className="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-blue-700 text-white rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all shadow-md hover:shadow-lg flex items-center gap-2"
              >
                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Nouveau produit
              </Link>
            </div>
          </div>
        </div>
      </div>

      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {/* Stats Grid - Professional Cards */}
        <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
          {/* Total Orders */}
          <div className="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow duration-300">
            <div className="flex items-center justify-between mb-4">
              <div className="w-14 h-14 bg-gradient-to-br from-blue-500 to-blue-700 rounded-xl flex items-center justify-center shadow-lg">
                <svg className="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                </svg>
              </div>
              <span className="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">
                +12% ce mois
              </span>
            </div>
            <p className="text-sm text-gray-500 font-medium">Total Commandes</p>
            <p className="text-3xl font-bold text-gray-900 mt-1">{stats.total}</p>
            <div className="mt-3 h-1.5 bg-gray-100 rounded-full overflow-hidden">
              <div className="h-full bg-gradient-to-r from-blue-500 to-blue-600 rounded-full" style={{ width: '75%' }}></div>
            </div>
          </div>

          {/* Total Sales */}
          <div className="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow duration-300">
            <div className="flex items-center justify-between mb-4">
              <div className="w-14 h-14 bg-gradient-to-br from-green-500 to-green-700 rounded-xl flex items-center justify-center shadow-lg">
                <svg className="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <span className={`text-xs font-medium px-2 py-1 rounded-full ${revenueChange >= 0 ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50'}`}>
                {revenueChange >= 0 ? '+' : ''}{revenueChange.toFixed(1)}%
              </span>
            </div>
            <p className="text-sm text-gray-500 font-medium">Ventes Totales</p>
            <p className="text-3xl font-bold text-gray-900 mt-1">{stats.totalSales.toFixed(2)} $</p>
            <div className="mt-3 flex items-center gap-2 text-xs text-gray-500">
              <span className="flex items-center gap-1">
                <svg className="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M5.293 9.707a1 1 0 010-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 01-1.414 1.414L11 7.414V15a1 1 0 11-2 0V7.414L6.707 9.707a1 1 0 01-1.414 0z" clipRule="evenodd" />
                </svg>
                vs mois dernier
              </span>
            </div>
          </div>

          {/* Clients */}
          <div className="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow duration-300">
            <div className="flex items-center justify-between mb-4">
              <div className="w-14 h-14 bg-gradient-to-br from-purple-500 to-purple-700 rounded-xl flex items-center justify-center shadow-lg">
                <svg className="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
              </div>
              <span className="text-xs font-medium text-green-600 bg-green-50 px-2 py-1 rounded-full">
                +5 nouveaux
              </span>
            </div>
            <p className="text-sm text-gray-500 font-medium">Clients Inscrits</p>
            <p className="text-3xl font-bold text-gray-900 mt-1">{usersStats.clients}</p>
            <div className="mt-3 flex items-center gap-4 text-xs">
              <span className="text-gray-500">
                <span className="font-semibold text-purple-600">{usersStats.livreurs}</span> livreurs
              </span>
            </div>
          </div>

          {/* Pending */}
          <div className="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 hover:shadow-xl transition-shadow duration-300">
            <div className="flex items-center justify-between mb-4">
              <div className="w-14 h-14 bg-gradient-to-br from-orange-500 to-orange-700 rounded-xl flex items-center justify-center shadow-lg">
                <svg className="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
              </div>
              <span className="text-xs font-medium text-orange-600 bg-orange-50 px-2 py-1 rounded-full">
                Action requise
              </span>
            </div>
            <p className="text-sm text-gray-500 font-medium">En attente</p>
            <p className="text-3xl font-bold text-gray-900 mt-1">{stats.enAttente}</p>
            <div className="mt-3 flex items-center gap-2">
              <span className="text-xs text-orange-600">{stats.paye} payées</span>
              <span className="text-gray-300">•</span>
              <span className="text-xs text-blue-600">{stats.enPreparation} en préparation</span>
            </div>
          </div>
        </div>

        {/* Orders by Status - Visual Bar */}
        <div className="bg-white rounded-2xl shadow-lg border border-gray-100 p-6 mb-8">
          <h2 className="text-lg font-semibold text-gray-900 mb-6">Répartition des commandes</h2>
          <div className="grid grid-cols-2 md:grid-cols-6 gap-4">
            <div className="text-center">
              <div className="w-full bg-yellow-100 rounded-full h-4 mb-2 overflow-hidden">
                <div className="h-full bg-yellow-500 rounded-full" style={{ width: stats.total > 0 ? `${(stats.enAttente / stats.total) * 100}%` : '0%' }}></div>
              </div>
              <p className="text-2xl font-bold text-yellow-600">{stats.enAttente}</p>
              <p className="text-xs text-gray-500">En attente</p>
            </div>
            <div className="text-center">
              <div className="w-full bg-blue-100 rounded-full h-4 mb-2 overflow-hidden">
                <div className="h-full bg-blue-500 rounded-full" style={{ width: stats.total > 0 ? `${(stats.paye / stats.total) * 100}%` : '0%' }}></div>
              </div>
              <p className="text-2xl font-bold text-blue-600">{stats.paye}</p>
              <p className="text-xs text-gray-500">Payées</p>
            </div>
            <div className="text-center">
              <div className="w-full bg-purple-100 rounded-full h-4 mb-2 overflow-hidden">
                <div className="h-full bg-purple-500 rounded-full" style={{ width: stats.total > 0 ? `${(stats.enPreparation / stats.total) * 100}%` : '0%' }}></div>
              </div>
              <p className="text-2xl font-bold text-purple-600">{stats.enPreparation}</p>
              <p className="text-xs text-gray-500">Préparation</p>
            </div>
            <div className="text-center">
              <div className="w-full bg-orange-100 rounded-full h-4 mb-2 overflow-hidden">
                <div className="h-full bg-orange-500 rounded-full" style={{ width: stats.total > 0 ? `${(stats.enLivraison / stats.total) * 100}%` : '0%' }}></div>
              </div>
              <p className="text-2xl font-bold text-orange-600">{stats.enLivraison}</p>
              <p className="text-xs text-gray-500">En livraison</p>
            </div>
            <div className="text-center">
              <div className="w-full bg-green-100 rounded-full h-4 mb-2 overflow-hidden">
                <div className="h-full bg-green-500 rounded-full" style={{ width: stats.total > 0 ? `${(stats.livre / stats.total) * 100}%` : '0%' }}></div>
              </div>
              <p className="text-2xl font-bold text-green-600">{stats.livre}</p>
              <p className="text-xs text-gray-500">Livrées</p>
            </div>
          </div>
        </div>

        <div className="grid grid-cols-1 lg:grid-cols-3 gap-8">
          {/* Recent Orders */}
          <div className="lg:col-span-2 bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div className="p-6 border-b border-gray-100 flex justify-between items-center">
              <h2 className="text-lg font-semibold text-gray-900">Commandes récentes</h2>
              <Link href="/admin/commandes" className="text-sm text-blue-600 hover:text-blue-700 font-medium flex items-center gap-1">
                Voir tout
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5l7 7-7 7" />
                </svg>
              </Link>
            </div>
            <div className="divide-y divide-gray-100">
              {recentOrders.length > 0 ? (
                recentOrders.map((order) => (
                  <div key={order.id} className="p-4 hover:bg-gray-50 transition-colors">
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-4">
                        <div className="w-10 h-10 bg-gradient-to-br from-gray-100 to-gray-200 rounded-lg flex items-center justify-center">
                          <span className="text-sm font-bold text-gray-600">#{order.id}</span>
                        </div>
                        <div>
                          <p className="font-semibold text-gray-900">{order.customerName}</p>
                          <p className="text-sm text-gray-500">{order.customerCity}</p>
                        </div>
                      </div>
                      <div className="text-right">
                        <p className="font-bold text-gray-900">{order.totalAmount.toFixed(2)} $</p>
                        <span className={`inline-block px-2.5 py-1 rounded-full text-xs font-medium border ${statusColors[order.status]}`}>
                          {statusLabels[order.status]}
                        </span>
                      </div>
                    </div>
                  </div>
                ))
              ) : (
                <div className="p-12 text-center">
                  <svg className="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                  </svg>
                  <p className="text-gray-500">Aucune commande</p>
                </div>
              )}
            </div>
          </div>

          {/* Quick Actions */}
          <div className="bg-white rounded-2xl shadow-lg border border-gray-100 p-6">
            <h2 className="text-lg font-semibold text-gray-900 mb-6">Actions rapides</h2>
            <div className="space-y-3">
              <Link
                href="/admin/produits"
                className="flex items-center p-4 bg-gradient-to-r from-blue-50 to-blue-100/50 rounded-xl hover:from-blue-100 hover:to-blue-100 transition-all group border border-blue-200"
              >
                <div className="w-10 h-10 bg-blue-600 rounded-lg flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                  <svg className="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                  </svg>
                </div>
                <div className="ml-3">
                  <p className="font-semibold text-gray-900">Ajouter un produit</p>
                  <p className="text-xs text-gray-500">Créer un nouveau produit</p>
                </div>
              </Link>
              
              <Link
                href="/admin/commandes"
                className="flex items-center p-4 bg-gradient-to-r from-purple-50 to-purple-100/50 rounded-xl hover:from-purple-100 hover:to-purple-100 transition-all group border border-purple-200"
              >
                <div className="w-10 h-10 bg-purple-600 rounded-lg flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                  <svg className="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                  </svg>
                </div>
                <div className="ml-3">
                  <p className="font-semibold text-gray-900">Gérer les commandes</p>
                  <p className="text-xs text-gray-500">{stats.enAttente} en attente</p>
                </div>
              </Link>

              <Link
                href="/categories"
                className="flex items-center p-4 bg-gradient-to-r from-green-50 to-green-100/50 rounded-xl hover:from-green-100 hover:to-green-100 transition-all group border border-green-200"
              >
                <div className="w-10 h-10 bg-green-600 rounded-lg flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                  <svg className="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                  </svg>
                </div>
                <div className="ml-3">
                  <p className="font-semibold text-gray-900">Catégories</p>
                  <p className="text-xs text-gray-500">{categories.length} catégories</p>
                </div>
              </Link>

              <Link
                href="/produits"
                className="flex items-center p-4 bg-gradient-to-r from-orange-50 to-orange-100/50 rounded-xl hover:from-orange-100 hover:to-orange-100 transition-all group border border-orange-200"
              >
                <div className="w-10 h-10 bg-orange-600 rounded-lg flex items-center justify-center shadow-md group-hover:scale-110 transition-transform">
                  <svg className="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                  </svg>
                </div>
                <div className="ml-3">
                  <p className="font-semibold text-gray-900">Produits</p>
                  <p className="text-xs text-gray-500">{products.length} produits</p>
                </div>
              </Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}
