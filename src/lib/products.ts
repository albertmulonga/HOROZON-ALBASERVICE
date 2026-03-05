"use server";

import { db } from "@/db";
import { products, categories, users } from "@/db/schema";
import { eq, desc, like, and, gt } from "drizzle-orm";
import { revalidatePath } from "next/cache";
import { hashPassword } from "@/lib/auth";

export async function getProducts(categoryId?: number, search?: string) {
  let query = db.select().from(products).where(eq(products.isActive, true));
  
  if (categoryId) {
    query = db.select().from(products).where(
      and(eq(products.isActive, true), eq(products.categoryId, categoryId))
    );
  }
  
  if (search) {
    query = db.select().from(products).where(
      and(eq(products.isActive, true), like(products.name, `%${search}%`))
    );
  }
  
  return await query.orderBy(desc(products.createdAt));
}

export async function getPopularProducts() {
  return await db.select().from(products)
    .where(and(eq(products.isActive, true), eq(products.isPopular, true)))
    .orderBy(desc(products.createdAt))
    .limit(8);
}

export async function getPromotionProducts() {
  return await db.select().from(products)
    .where(and(eq(products.isActive, true), eq(products.isPromotion, true)))
    .orderBy(desc(products.createdAt));
}

export async function getProductById(id: number) {
  return await db.query.products.findFirst({
    where: eq(products.id, id),
  });
}

export async function getAllProducts() {
  return await db.select().from(products).orderBy(desc(products.createdAt));
}

export async function getCategories() {
  return await db.select().from(categories).where(eq(categories.isActive, true));
}

export async function getCategoryById(id: number) {
  return await db.query.categories.findFirst({
    where: eq(categories.id, id),
  });
}

// Admin actions
export async function createProduct(formData: FormData) {
  const name = formData.get("name") as string;
  const description = formData.get("description") as string;
  const price = parseFloat(formData.get("price") as string);
  const categoryId = parseInt(formData.get("categoryId") as string);
  const stock = parseInt(formData.get("stock") as string);
  const isPopular = formData.get("isPopular") === "on";
  const isPromotion = formData.get("isPromotion") === "on";
  const originalPrice = formData.get("originalPrice") ? parseFloat(formData.get("originalPrice") as string) : null;

  if (!name || !price) {
    return { error: "Veuillez remplir tous les champs obligatoires" };
  }

  await db.insert(products).values({
    name,
    description: description || null,
    price,
    categoryId: categoryId || null,
    stock: stock || 0,
    isPopular,
    isPromotion,
    originalPrice,
    image: "/images/products/placeholder.jpg",
  });

  revalidatePath("/admin");
  revalidatePath("/produits");
  return { success: true };
}

export async function updateProduct(id: number, formData: FormData) {
  const name = formData.get("name") as string;
  const description = formData.get("description") as string;
  const price = parseFloat(formData.get("price") as string);
  const categoryId = parseInt(formData.get("categoryId") as string);
  const stock = parseInt(formData.get("stock") as string);
  const isPopular = formData.get("isPopular") === "on";
  const isPromotion = formData.get("isPromotion") === "on";
  const originalPrice = formData.get("originalPrice") ? parseFloat(formData.get("originalPrice") as string) : null;

  await db.update(products).set({
    name,
    description: description || null,
    price,
    categoryId: categoryId || null,
    stock: stock || 0,
    isPopular,
    isPromotion,
    originalPrice,
  }).where(eq(products.id, id));

  revalidatePath("/admin");
  revalidatePath("/produits");
  return { success: true };
}

export async function deleteProduct(id: number) {
  await db.update(products).set({ isActive: false }).where(eq(products.id, id));
  revalidatePath("/admin");
  revalidatePath("/produits");
  return { success: true };
}

export async function createCategory(formData: FormData) {
  const name = formData.get("name") as string;
  const description = formData.get("description") as string;

  if (!name) {
    return { error: "Le nom de la catégorie est requis" };
  }

  await db.insert(categories).values({
    name,
    description: description || null,
    image: "/images/categories/placeholder.jpg",
  });

  revalidatePath("/admin");
  revalidatePath("/categories");
  return { success: true };
}

export async function deleteCategory(id: number) {
  await db.update(categories).set({ isActive: false }).where(eq(categories.id, id));
  revalidatePath("/admin");
  revalidatePath("/categories");
  return { success: true };
}

// Initialize default data (categories, products, settings)
export async function initializeData() {
  const existingCategories = await db.select().from(categories).limit(1);
  
  // Always ensure admin exists
  const existingAdmin = await db.select().from(users).where(eq(users.email, "vente@gmail.com")).limit(1);
  if (existingAdmin.length === 0) {
    const adminPassword = await hashPassword("admin.com");
    await db.insert(users).values({
      name: "Administrateur",
      email: "vente@gmail.com",
      phone: "+243 000 000 001",
      password: adminPassword,
      role: "admin",
    });
  }

  if (existingCategories.length > 0) return;

  // Create default categories
  const defaultCategories = [
    { name: "Vêtements", description: "Vêtements pour hommes, femmes et enfants" },
    { name: "Sacs", description: "Sacs à main, sacs à dos, sacs de voyage" },
    { name: "Chaussures", description: "Chaussures pour toutes les occasions" },
    { name: "Accessoires", description: "Montres, bijoux, ceintures et plus" },
  ];

  const categoryIds: number[] = [];
  for (const cat of defaultCategories) {
    const result = await db.insert(categories).values({
      name: cat.name,
      description: cat.description,
      image: "/images/categories/placeholder.jpg",
    }).returning();
    categoryIds.push(result[0].id);
  }

  // Create sample products
  const sampleProducts = [
    { name: "Chemise Elegante", description: "Chemise de qualité supérieure", price: 25.00, categoryId: categoryIds[0], stock: 50, isPopular: true, isPromotion: false },
    { name: "Pantalon Classique", description: "Pantalon confortable et élégant", price: 35.00, categoryId: categoryIds[0], stock: 30, isPopular: true, isPromotion: false },
    { name: "Robe Moderna", description: "Robe élégante pour toutes occasions", price: 45.00, categoryId: categoryIds[0], stock: 25, isPopular: true, isPromotion: true, originalPrice: 55.00 },
    { name: "Sac à Main Luxe", description: "Sac à main de marque", price: 60.00, categoryId: categoryIds[1], stock: 20, isPopular: true, isPromotion: false },
    { name: "Sac à Dos", description: "Sac à dos résistant et pratique", price: 30.00, categoryId: categoryIds[1], stock: 40, isPopular: false, isPromotion: false },
    { name: "Chaussures Sport", description: "Chaussures de sport de qualité", price: 55.00, categoryId: categoryIds[2], stock: 35, isPopular: true, isPromotion: false },
    { name: "Sandales", description: "Sandales élégantes et confortables", price: 20.00, categoryId: categoryIds[2], stock: 45, isPopular: false, isPromotion: true, originalPrice: 28.00 },
    { name: "Montre Connectée", description: "Montre intelligente avec toutes les fonctionnalités", price: 80.00, categoryId: categoryIds[3], stock: 15, isPopular: true, isPromotion: false },
  ];

  for (const prod of sampleProducts) {
    await db.insert(products).values({
      ...prod,
      image: "/images/products/placeholder.jpg",
    });
  }
}

// Initialize site settings
export async function initializeSettings() {
  // This function can be expanded to create default settings
  // For now, it's a placeholder
}
