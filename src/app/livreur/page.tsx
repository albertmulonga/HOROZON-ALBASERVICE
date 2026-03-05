import { redirect } from "next/navigation";
import { getCurrentUser } from "@/lib/auth";
import { getDeliveryPersonOrders } from "@/lib/orders";

export default async function LivreurDashboard() {
  const user = await getCurrentUser();
  
  if (!user || user.role !== "livreur") {
    redirect("/login");
  }

  const orders = await getDeliveryPersonOrders(user.id);

  const statusColors: Record<string, string> = {
    en_attente: "bg-yellow-100 text-yellow-800",
    paye: "bg-blue-100 text-blue-800",
    en_preparation: "bg-purple-100 text-purple-800",
    en_livraison: "bg-orange-100 text-orange-800",
    livre: "bg-green-100 text-green-800",
    annule: "bg-red-100 text-red-800",
  };

  const activeOrders = orders.filter(o => o.status === "paye" || o.status === "en_preparation" || o.status === "en_livraison");
  const completedOrders = orders.filter(o => o.status === "livre");

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div className="flex justify-between items-center mb-8">
        <div>
          <h1 className="text-3xl font-bold text-gray-900">Tableau de bord Livreur</h1>
          <p className="text-gray-600">Bienvenue, {user.name}</p>
        </div>
      </div>

      {/* Stats */}
      <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div className="bg-white rounded-lg shadow-md p-6">
          <p className="text-sm text-gray-500">Commandes à livrer</p>
          <p className="text-3xl font-bold text-orange-600">{activeOrders.length}</p>
        </div>
        <div className="bg-white rounded-lg shadow-md p-6">
          <p className="text-sm text-gray-500">Livraisons terminées</p>
          <p className="text-3xl font-bold text-green-600">{completedOrders.length}</p>
        </div>
        <div className="bg-white rounded-lg shadow-md p-6">
          <p className="text-sm text-gray-500">Total</p>
          <p className="text-3xl font-bold text-blue-600">{orders.length}</p>
        </div>
      </div>

      {/* Orders to Deliver */}
      <div className="bg-white rounded-lg shadow-md overflow-hidden">
        <div className="p-6 border-b border-gray-200">
          <h2 className="text-xl font-semibold">Commandes à livrer</h2>
        </div>

        {activeOrders.length > 0 ? (
          <div className="divide-y divide-gray-200">
            {activeOrders.map((order) => (
              <div key={order.id} className="p-6">
                <div className="flex flex-col md:flex-row md:items-center justify-between gap-4">
                  <div>
                    <div className="flex items-center gap-4 mb-2">
                      <span className="text-lg font-semibold">Commande #{order.id}</span>
                      <span className={`px-3 py-1 rounded-full text-sm font-medium ${statusColors[order.status]}`}>
                        {order.status}
                      </span>
                    </div>
                    <p className="text-gray-600">
                      <strong>Client:</strong> {order.customerName} - {order.customerPhone}
                    </p>
                    <p className="text-gray-600">
                      <strong>Adresse:</strong> {order.customerAddress}, {order.customerCity}
                    </p>
                    {order.customerLatitude && order.customerLongitude && (
                      <a
                        href={`https://www.google.com/maps/dir/?api=1&destination=${order.customerLatitude},${order.customerLongitude}`}
                        target="_blank"
                        rel="noopener noreferrer"
                        className="inline-block mt-2 text-blue-600 hover:underline"
                      >
                        📍 Voir sur Google Maps
                      </a>
                    )}
                  </div>
                  
                  <div className="text-right">
                    <p className="text-2xl font-bold">{order.totalAmount.toFixed(2)} $</p>
                    {order.status === "en_livraison" ? (
                      <button className="mt-2 px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                        Marquer comme livré
                      </button>
                    ) : order.status === "paye" || order.status === "en_preparation" ? (
                      <button className="mt-2 px-4 py-2 bg-orange-500 text-white rounded-lg hover:bg-orange-600">
                        Commencer la livraison
                      </button>
                    ) : null}
                  </div>
                </div>
              </div>
            ))}
          </div>
        ) : (
          <div className="p-12 text-center">
            <p className="text-gray-500">Aucune commande à livrer pour le moment</p>
          </div>
        )}
      </div>

      {/* Completed Deliveries */}
      {completedOrders.length > 0 && (
        <div className="bg-white rounded-lg shadow-md overflow-hidden mt-8">
          <div className="p-6 border-b border-gray-200">
            <h2 className="text-xl font-semibold">Livraisons terminées</h2>
          </div>
          <div className="divide-y divide-gray-200">
            {completedOrders.map((order) => (
              <div key={order.id} className="p-4 flex justify-between items-center">
                <div>
                  <p className="font-medium">Commande #{order.id}</p>
                  <p className="text-sm text-gray-500">{order.customerName} - {order.customerCity}</p>
                </div>
                <div className="text-right">
                  <span className="px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    Livré
                  </span>
                  <p className="text-sm font-medium mt-1">{order.totalAmount.toFixed(2)} $</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      )}
    </div>
  );
}
