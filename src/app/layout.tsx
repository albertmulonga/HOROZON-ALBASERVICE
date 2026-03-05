import type { Metadata } from "next";
import { Inter } from "next/font/google";
import "./globals.css";
import Header from "@/components/layout/Header";
import Footer from "@/components/layout/Footer";
import { getUser } from "@/lib/auth";

const inter = Inter({ subsets: ["latin"] });

export const metadata: Metadata = {
  title: "HOROZON ALBASERVICE - Votre Boutique en Ligne",
  description: "Plateforme e-commerce de confiance à Kindu. Vêtements, sacs, chaussures et accessoires de qualité.",
};

export default async function RootLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  const user = await getUser();

  // Transform null to undefined for type compatibility
  const headerUser = user ? {
    ...user,
    phone: user.phone ?? undefined,
    profileImage: user.profileImage ?? undefined,
    address: user.address ?? undefined,
    city: user.city ?? undefined,
  } : null;

  return (
    <html lang="fr">
      <body className={inter.className}>
        <Header user={headerUser} />
        <main className="min-h-screen bg-gray-50">
          {children}
        </main>
        <Footer />
      </body>
    </html>
  );
}
