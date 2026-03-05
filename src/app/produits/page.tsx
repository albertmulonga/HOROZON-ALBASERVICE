import Link from "next/link";
import { getProducts, getCategories } from "@/lib/products";
import ProductCard from "@/components/ui/ProductCard";

export default async function ProductsPage({
  searchParams,
}: {
  searchParams: Promise<{ category?: string; search?: string }>;
}) {
  const params = await searchParams;
  const categoryId = params.category ? parseInt(params.category) : undefined;
  const search = params.search;
  
  const products = await getProducts(categoryId, search);
  const categories = await getCategories();

  return (
    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      {/* Breadcrumb */}
      <nav className="text-sm mb-6">
        <ol className="flex items-center space-x-2">
          <li>
            <Link href="/" className="text-gray-500 hover:text-blue-600">
              Accueil
            </Link>
          </li>
          <li className="text-gray-400">/</li>
          <li className="text-gray-900 font-medium">Produits</li>
        </ol>
      </nav>

      <div className="flex flex-col lg:flex-row gap-8">
        {/* Sidebar - Categories */}
        <aside className="lg:w-64 flex-shrink-0">
          <div className="bg-white rounded-lg shadow-md p-6">
            <h3 className="text-lg font-semibold mb-4">Catégories</h3>
            <ul className="space-y-2">
              <li>
                <Link
                  href="/produits"
                  className={`block py-2 px-3 rounded ${
                    !categoryId ? "bg-blue-50 text-blue-600 font-medium" : "text-gray-700 hover:bg-gray-50"
                  }`}
                >
                  Tous les produits
                </Link>
              </li>
              {categories.map((category) => (
                <li key={category.id}>
                  <Link
                    href={`/produits?category=${category.id}`}
                    className={`block py-2 px-3 rounded ${
                      categoryId === category.id
                        ? "bg-blue-50 text-blue-600 font-medium"
                        : "text-gray-700 hover:bg-gray-50"
                    }`}
                  >
                    {category.name}
                  </Link>
                </li>
              ))}
            </ul>
          </div>
        </aside>

        {/* Main Content */}
        <div className="flex-1">
          {/* Search & Filter Bar */}
          <div className="bg-white rounded-lg shadow-md p-4 mb-6">
            <form action="/produits" method="GET" className="flex gap-4">
              {categoryId && <input type="hidden" name="category" value={categoryId} />}
              <div className="flex-1">
                <input
                  type="text"
                  name="search"
                  defaultValue={search}
                  placeholder="Rechercher un produit..."
                  className="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                />
              </div>
              <button
                type="submit"
                className="px-6 py-2 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700"
              >
                Rechercher
              </button>
            </form>
          </div>

          {/* Results Count */}
          <div className="mb-6">
            <p className="text-gray-600">
              {products.length} produit{products.length !== 1 ? "s" : ""} trouvé{products.length !== 1 ? "s" : ""}
            </p>
          </div>

          {/* Products Grid */}
          {products.length > 0 ? (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
              {products.map((product) => (
                <ProductCard key={product.id} product={product} />
              ))}
            </div>
          ) : (
            <div className="text-center py-12 bg-white rounded-lg shadow-md">
              <svg className="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={1} d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <p className="text-gray-500 text-lg">Aucun produit trouvé</p>
              <Link href="/produits" className="mt-4 inline-block text-blue-600 hover:underline">
                Voir tous les produits
              </Link>
            </div>
          )}
        </div>
      </div>
    </div>
  );
}
