import Link from "next/link";
import { getCategories, getProducts } from "@/lib/products";
import ProductCard from "@/components/ui/ProductCard";

export default async function CategoriesPage({
  searchParams,
}: {
  searchParams: Promise<{ id?: string }>;
}) {
  const params = await searchParams;
  const categoryId = params.id ? parseInt(params.id) : undefined;
  
  const categories = await getCategories();
  const products = categoryId ? await getProducts(categoryId) : [];
  const selectedCategory = categoryId 
    ? categories.find(c => c.id === categoryId) 
    : null;

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
          <li className="text-gray-900 font-medium">Catégories</li>
        </ol>
      </nav>

      <h1 className="text-3xl font-bold mb-8">Nos Catégories</h1>

      {/* Categories Grid */}
      <div className="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-6 mb-12">
        {categories.map((category) => (
          <Link
            key={category.id}
            href={`/categories?id=${category.id}`}
            className={`bg-white rounded-lg shadow-md p-6 text-center hover:shadow-xl transition-shadow ${
              categoryId === category.id ? "ring-2 ring-blue-500" : ""
            }`}
          >
            <div className="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
              <svg className="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
              </svg>
            </div>
            <h3 className="font-semibold text-gray-800">{category.name}</h3>
            {category.description && (
              <p className="text-sm text-gray-500 mt-2 line-clamp-2">{category.description}</p>
            )}
          </Link>
        ))}
      </div>

      {/* Products for Selected Category */}
      {selectedCategory && (
        <div>
          <h2 className="text-2xl font-bold mb-6">
            Produits dans "{selectedCategory.name}"
          </h2>
          
          {products.length > 0 ? (
            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
              {products.map((product) => (
                <ProductCard key={product.id} product={product} />
              ))}
            </div>
          ) : (
            <div className="text-center py-12 bg-white rounded-lg shadow-md">
              <p className="text-gray-500">Aucun produit dans cette catégorie</p>
            </div>
          )}
        </div>
      )}

      {!selectedCategory && (
        <div className="text-center py-12 bg-gray-50 rounded-lg">
          <p className="text-gray-600">
            Sélectionnez une catégorie pour voir les produits
          </p>
        </div>
      )}
    </div>
  );
}
