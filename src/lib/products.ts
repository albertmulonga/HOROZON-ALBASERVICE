"use server";

import { db } from "@/db";
import { products, categories } from "@/db/schema";
import { eq, desc, like, and, gt } from "drizzle-orm";
import { revalidatePath } from "next/cache";

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
