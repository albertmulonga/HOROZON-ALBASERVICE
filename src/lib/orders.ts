"use server";

import { db } from "@/db";
import { orders, orderDetails, payments, users, deliveryLocations } from "@/db/schema";
import { eq, desc, and } from "drizzle-orm";
import { revalidatePath } from "next/cache";

export interface CartItem {
  productId: number;
  name: string;
  price: number;
  quantity: number;
  image?: string;
}

export async function createOrder(
  userId: number,
  customerName: string,
  customerPhone: string,
  customerAddress: string,
  customerCity: string,
  customerLatitude: number | null,
  customerLongitude: number | null,
  items: CartItem[]
) {
  const totalAmount = items.reduce((sum, item) => sum + (item.price * item.quantity), 0);

  const [order] = await db.insert(orders).values({
    userId,
    customerName,
    customerPhone,
    customerAddress,
    customerCity,
    customerLatitude,
    customerLongitude,
    totalAmount,
    status: "en_attente",
  }).returning();

  // Create order details
  for (const item of items) {
    await db.insert(orderDetails).values({
      orderId: order.id,
      productId: item.productId,
      productName: item.name,
      productPrice: item.price,
      quantity: item.quantity,
      subtotal: item.price * item.quantity,
    });
  }

  revalidatePath("/client");
  return order;
}

export async function getOrdersByUser(userId: number) {
  return await db.select().from(orders)
    .where(eq(orders.userId, userId))
    .orderBy(desc(orders.createdAt));
}

export async function getOrderById(orderId: number) {
  const order = await db.query.orders.findFirst({
    where: eq(orders.id, orderId),
  });
  
  if (order) {
    const details = await db.select().from(orderDetails).where(eq(orderDetails.orderId, orderId));
    return { ...order, details };
  }
  
  return null;
}

export async function getAllOrders() {
  return await db.select().from(orders).orderBy(desc(orders.createdAt));
}

export async function getPendingOrders() {
  return await db.select().from(orders)
    .where(eq(orders.status, "en_attente"))
    .orderBy(desc(orders.createdAt));
}

export async function getPaidOrders() {
  return await db.select().from(orders)
    .where(eq(orders.status, "paye"))
    .orderBy(desc(orders.createdAt));
}

export async function getOrdersInDelivery() {
  return await db.select().from(orders)
    .where(eq(orders.status, "en_livraison"))
    .orderBy(desc(orders.createdAt));
}

// Payment functions
export async function createPayment(
  orderId: number,
  amount: number,
  transactionNumber: string,
  paymentPhone: string
) {
  const [payment] = await db.insert(payments).values({
    orderId,
    amount,
    transactionNumber,
    paymentPhone,
    paymentMethod: "mobile_money",
    status: "en_attente",
  }).returning();

  return payment;
}

export async function getPaymentByOrder(orderId: number) {
  return await db.query.payments.findFirst({
    where: eq(payments.orderId, orderId),
  });
}

export async function validatePayment(paymentId: number, adminId: number) {
  const payment = await db.query.payments.findFirst({
    where: eq(payments.id, paymentId),
  });

  if (payment) {
    await db.update(payments).set({
      status: "valide",
      validatedAt: new Date(),
      validatedBy: adminId,
    }).where(eq(payments.id, paymentId));

    // Update order status
    await db.update(orders).set({
      status: "paye",
    }).where(eq(orders.id, payment.orderId));

    revalidatePath("/admin");
    return { success: true };
  }

  return { error: "Paiement non trouvé" };
}

export async function rejectPayment(paymentId: number) {
  await db.update(payments).set({
    status: "rejete",
  }).where(eq(payments.id, paymentId));

  revalidatePath("/admin");
  return { success: true };
}

// Order status management
export async function updateOrderStatus(orderId: number, status: string) {
  await db.update(orders).set({ status: status as any }).where(eq(orders.id, orderId));
  revalidatePath("/admin");
  revalidatePath("/client");
  return { success: true };
}

export async function assignDeliveryPerson(orderId: number, deliveryPersonId: number) {
  await db.update(orders).set({
    deliveryPersonId,
    status: "en_preparation",
  }).where(eq(orders.id, orderId));
  
  revalidatePath("/admin");
  revalidatePath("/livreur");
  return { success: true };
}

export async function startDelivery(orderId: number) {
  await db.update(orders).set({ status: "en_livraison" }).where(eq(orders.id, orderId));
  revalidatePath("/livreur");
  return { success: true };
}

export async function completeDelivery(orderId: number) {
  await db.update(orders).set({ status: "livre" }).where(eq(orders.id, orderId));
  revalidatePath("/livreur");
  revalidatePath("/client");
  return { success: true };
}

// Delivery person functions
export async function getDeliveryPersonOrders(deliveryPersonId: number) {
  return await db.select().from(orders)
    .where(eq(orders.deliveryPersonId, deliveryPersonId))
    .orderBy(desc(orders.createdAt));
}

export async function getAvailableDeliveryPersons() {
  return await db.select().from(users).where(eq(users.role, "livreur"));
}

// Location tracking
export async function updateDeliveryLocation(
  orderId: number,
  deliveryPersonId: number,
  latitude: number,
  longitude: number,
  accuracy?: number,
  speed?: number,
  heading?: number
) {
  await db.insert(deliveryLocations).values({
    orderId,
    deliveryPersonId,
    latitude,
    longitude,
    accuracy: accuracy || null,
    speed: speed || null,
    heading: heading || null,
  });

  return { success: true };
}

export async function getLatestDeliveryLocation(orderId: number) {
  return await db.query.deliveryLocations.findFirst({
    where: eq(deliveryLocations.orderId, orderId),
    orderBy: desc(deliveryLocations.timestamp),
  });
}

export async function getDeliveryLocations(orderId: number) {
  return await db.select().from(deliveryLocations)
    .where(eq(deliveryLocations.orderId, orderId))
    .orderBy(desc(deliveryLocations.timestamp));
}

// Statistics
export async function getOrderStats() {
  const allOrders = await db.select().from(orders);
  
  const stats = {
    total: allOrders.length,
    enAttente: allOrders.filter(o => o.status === "en_attente").length,
    paye: allOrders.filter(o => o.status === "paye").length,
    enPreparation: allOrders.filter(o => o.status === "en_preparation").length,
    enLivraison: allOrders.filter(o => o.status === "en_livraison").length,
    livre: allOrders.filter(o => o.status === "livre").length,
    totalSales: allOrders.filter(o => o.status === "livre").reduce((sum, o) => sum + o.totalAmount, 0),
  };
  
  return stats;
}

export async function getUsersCount() {
  const allUsers = await db.select().from(users);
  return {
    total: allUsers.length,
    clients: allUsers.filter(u => u.role === "client").length,
    livreurs: allUsers.filter(u => u.role === "livreur").length,
  };
}
