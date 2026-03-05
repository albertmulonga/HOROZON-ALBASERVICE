import { NextResponse } from "next/server";
import { db } from "@/db";
import { orders, orderDetails, payments, users } from "@/db/schema";
import { eq } from "drizzle-orm";

export async function POST(request: Request) {
  try {
    const body = await request.json();
    const { 
      customerName, 
      customerPhone, 
      customerAddress, 
      customerCity, 
      latitude, 
      longitude,
      transactionNumber,
      items 
    } = body;

    if (!customerName || !customerPhone || !customerAddress || !customerCity || !items || items.length === 0) {
      return NextResponse.json(
        { success: false, error: "Informations incomplètes" },
        { status: 400 }
      );
    }

    // Check if user is logged in
    const cookieHeader = request.headers.get("cookie");
    let userId = null;
    
    if (cookieHeader) {
      const sessionMatch = cookieHeader.match(/session_id=([^;]+)/);
      if (sessionMatch) {
        const sessionValue = sessionMatch[1];
        userId = parseInt(sessionValue.split(":")[0]);
      }
    }

    // If no user logged in, create a guest user or find by phone
    if (!userId) {
      // Try to find user by phone
      let user = await db.query.users.findFirst({
        where: eq(users.phone, customerPhone),
      });
      
      if (!user) {
        // Create a guest user account
        const [newUser] = await db.insert(users).values({
          name: customerName,
          email: `guest_${Date.now()}@hirizon.com`,
          phone: customerPhone,
          password: "guest_account",
          role: "client",
        }).returning();
        
        userId = newUser.id;
      } else {
        userId = user.id;
      }
    }

    const totalAmount = items.reduce((sum: number, item: any) => 
      sum + (item.price * item.quantity), 0
    );

    // Create order
    const [order] = await db.insert(orders).values({
      userId,
      customerName,
      customerPhone,
      customerAddress,
      customerCity,
      customerLatitude: latitude || null,
      customerLongitude: longitude || null,
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

    // Create payment record
    await db.insert(payments).values({
      orderId: order.id,
      amount: totalAmount,
      transactionNumber: transactionNumber || "",
      paymentPhone: customerPhone,
      paymentMethod: "mobile_money",
      status: "en_attente",
    });

    return NextResponse.json({ 
      success: true, 
      orderId: order.id 
    });
  } catch (error) {
    console.error("Order creation error:", error);
    return NextResponse.json(
      { success: false, error: "Erreur lors de la commande" },
      { status: 500 }
    );
  }
}
