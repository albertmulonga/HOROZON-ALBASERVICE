"use server";

import { db } from "@/db";
import { users } from "@/db/schema";
import { eq, desc } from "drizzle-orm";
import { revalidatePath } from "next/cache";
import { redirect } from "next/navigation";
import { hashPassword } from "@/lib/auth";

export async function getAllUsers() {
  return await db.select().from(users).orderBy(desc(users.createdAt));
}

export async function getUsersByRole(role: "client" | "livreur" | "admin") {
  return await db.select().from(users).where(eq(users.role, role)).orderBy(desc(users.createdAt));
}

export async function createUserByAdmin(formData: FormData) {
  const name = formData.get("name") as string;
  const email = formData.get("email") as string;
  const phone = formData.get("phone") as string;
  const password = formData.get("password") as string;
  const role = formData.get("role") as string;
  const address = formData.get("address") as string;
  const city = formData.get("city") as string;

  if (!name || !email || !password || !role) {
    return { error: "Veuillez remplir tous les champs obligatoires" };
  }

  // Check if email already exists
  const existingUser = await db.query.users.findFirst({
    where: eq(users.email, email.toLowerCase()),
  });

  if (existingUser) {
    return { error: "Cet email est déjà utilisé" };
  }

  const passwordHash = await hashPassword(password);

  await db.insert(users).values({
    name,
    email: email.toLowerCase(),
    phone: phone || null,
    password: passwordHash,
    role: role as "client" | "admin" | "livreur",
    address: address || null,
    city: city || null,
  });

  revalidatePath("/admin/utilisateurs");
  return { success: true };
}

export async function toggleUserStatus(userId: number, isActive: boolean) {
  await db.update(users).set({ isActive: !isActive }).where(eq(users.id, userId));
  revalidatePath("/admin/utilisateurs");
}

export async function deleteUser(userId: number) {
  await db.delete(users).where(eq(users.id, userId));
  revalidatePath("/admin/utilisateurs");
}
