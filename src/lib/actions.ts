"use server";

import { db } from "@/db";
import { users, settings, categories, products } from "@/db/schema";
import { eq, desc } from "drizzle-orm";
import { hashPassword, createSession, destroySession, getCurrentUser } from "@/lib/auth";
import { revalidatePath } from "next/cache";
import { redirect } from "next/navigation";

// User Actions
export async function registerUser(formData: FormData) {
  const name = formData.get("name") as string;
  const email = formData.get("email") as string;
  const phone = formData.get("phone") as string;
  const password = formData.get("password") as string;
  const role = (formData.get("role") as string) || "client";

  if (!name || !email || !password) {
    return { error: "Veuillez remplir tous les champs obligatoires" };
  }

  // Check if email already exists
  const existingUser = await db.query.users.findFirst({
    where: eq(users.email, email),
  });

  if (existingUser) {
    return { error: "Cet email est déjà utilisé" };
  }

  const passwordHash = await hashPassword(password);

  const [newUser] = await db.insert(users).values({
    name,
    email,
    phone: phone || null,
    password: passwordHash,
    role: role as "client" | "admin" | "livreur",
  }).returning();

  await createSession(newUser.id);
  revalidatePath("/");
  
  if (role === "admin") {
    redirect("/admin");
  } else if (role === "livreur") {
    redirect("/livreur");
  } else {
    redirect("/client");
  }
}

export async function loginUser(formData: FormData) {
  const email = formData.get("email") as string;
  const password = formData.get("password") as string;

  if (!email || !password) {
    return { error: "Veuillez entrer votre email et mot de passe" };
  }

  const user = await db.query.users.findFirst({
    where: eq(users.email, email),
  });

  if (!user) {
    return { error: "Email ou mot de passe incorrect" };
  }

  const isValidPassword = await import("@/lib/auth").then(m => 
    m.verifyPassword(password, user.password)
  );

  if (!isValidPassword) {
    return { error: "Email ou mot de passe incorrect" };
  }

  await createSession(user.id);
  revalidatePath("/");

  if (user.role === "admin") {
    redirect("/admin");
  } else if (user.role === "livreur") {
    redirect("/livreur");
  } else {
    redirect("/client");
  }
}

export async function logoutUser() {
  await destroySession();
  revalidatePath("/");
  redirect("/");
}

// Get current logged in user
export async function getUser() {
  return await getCurrentUser();
}

// Settings Actions
export async function getSetting(key: string): Promise<string | null> {
  const setting = await db.query.settings.findFirst({
    where: eq(settings.key, key),
  });
  return setting?.value ?? null;
}

export async function setSetting(key: string, value: string) {
  await db.insert(settings).values({ key, value }).onConflictDoUpdate({
    target: settings.key,
    set: { value },
  });
  revalidatePath("/admin");
}

// Initialize default settings
export async function initializeSettings() {
  const existingPaymentNumber = await getSetting("payment_phone");
  
  if (!existingPaymentNumber) {
    await setSetting("payment_phone", "+243 000 000 000");
    await setSetting("shop_name", "HOROZON ALBASERVICE");
    await setSetting("shop_address", "Kindu, Congo");
  }
}

// Initialize categories and sample products
export async function initializeData() {
  const existingCategories = await db.query.categories.findFirst();
  
  if (!existingCategories) {
    // Create categories
    const categoriesData = [
      { name: "Vêtements", description: "Vêtements pour hommes et femmes", image: "/images/categories/vetements.jpg" },
      { name: "Sacs", description: "Sacs à main, sacs à dos", image: "/images/categories/sacs.jpg" },
      { name: "Chaussures", description: "Chaussures de toutes tailles", image: "/images/categories/chaussures.jpg" },
      { name: "Accessoires", description: "Montres, bijoux, ceintures", image: "/images/categories/accessoires.jpg" },
      { name: "Electronique", description: "Téléphones, accessoires", image: "/images/categories/electronique.jpg" },
    ];

    for (const cat of categoriesData) {
      await db.insert(categories).values(cat);
    }

    // Get categories
    const cats = await db.select().from(categories);

    // Create sample products
    const productsData = [
      { name: "Chemise elegante", description: "Chemise en coton de haute qualite", price: 25.00, categoryId: cats[0]?.id, stock: 50, isPopular: true },
      { name: "Pantalon classique", description: "Pantalon elegante pour homme", price: 35.00, categoryId: cats[0]?.id, stock: 40, isPopular: true },
      { name: "Robe feminine", description: "Robe elegante pour femme", price: 45.00, categoryId: cats[0]?.id, stock: 30, isPopular: true },
      { name: "Sac a main", description: "Sac a main en cuir", price: 55.00, categoryId: cats[1]?.id, stock: 25, isPopular: true },
      { name: "Sac a dos", description: "Sac a dos resistant", price: 30.00, categoryId: cats[1]?.id, stock: 35 },
      { name: "Chaussures homme", description: "Chaussures elegantes en cuir", price: 60.00, categoryId: cats[2]?.id, stock: 20, isPopular: true },
      { name: "Chaussures femme", description: "Talons elegantes", price: 50.00, categoryId: cats[2]?.id, stock: 25 },
      { name: "Montre elegante", description: "Montre automatique", price: 80.00, categoryId: cats[3]?.id, stock: 15, isPopular: true },
      { name: "Ceinture en cuir", description: "Ceinture de qualite", price: 20.00, categoryId: cats[3]?.id, stock: 40 },
      { name: "Telephone smartphone", description: "Smartphone 6 pouces", price: 150.00, categoryId: cats[4]?.id, stock: 10, isPopular: true },
    ];

    for (const prod of productsData) {
      await db.insert(products).values(prod);
    }

    // Create admin user
    const adminPassword = await hashPassword("admin.com");
    await db.insert(users).values({
      name: "Administrateur",
      email: "vente@gmail.com",
      phone: "+243 000 000 001",
      password: adminPassword,
      role: "admin",
    });
  }
}
