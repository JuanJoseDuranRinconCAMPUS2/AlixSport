import fondoImage from "../assets/fondo.png"
import Navbar from "../components/Nabar";
import ProductCard from "../components/ProductCard";
import Footer from "../components/Footer";
import { useEffect, useState } from "react";

import wheyImage from "../assets/wheyprotein.png";
import creatinaImage from "../assets/crea.png";
import preEntrenoImage from "../assets/preentreno.png";

export default function HomePage({ user, onLoginClick, onRegisterClick, onLogout }) {
  const [products, setProducts] = useState([]);

  useEffect(() => {
    setProducts([
      { id: 1, name: "Proteína Whey", price: 120000, image:  wheyImage },
      { id: 2, name: "Creatina Monohidratada", price: 85000, image: creatinaImage },
      { id: 3, name: "Pre-entreno", price: 95000, image: preEntrenoImage },
    ]);
  }, []);

  return (
    <div className="bg-neutral-900 text-white min-h-screen flex flex-col">
      <Navbar
        user={user}
        onLoginClick={onLoginClick}
        onRegisterClick={onRegisterClick}
        onLogout={onLogout}
      />

      <div
        className="w-full h-[500px] bg-cover bg-center flex items-center justify-center mt-24"
        style={{
          backgroundImage: `url(${fondoImage})`,
        }}
      >

        <div className="text-center">
          <h1 className="text-4xl font-bold bg-black/60 px-4 py-2 rounded-lg mb-4">
            {user ? `¡Bienvenido de vuelta!` : `Bienvenido a ALIX SUPLEMENTOS`}
          </h1>

          {user && (
            <p className="text-xl bg-black/60 px-4 py-2 rounded-lg">
              Hola, {user.nombre} - ¡Disfruta de tu experiencia!
            </p>
          )}
        </div>
      </div>

      <div className="container mx-auto px-6 py-8">
        <h2 className="text-3xl font-bold text-center mb-8">
          {user ? "Nuestros Productos Destacados" : "Explora Nuestros Productos"}
        </h2>
        <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
          {products.map((p) => (
            <ProductCard key={p.id} product={p} />
          ))}
        </div>
      </div>

      <Footer />
    </div>
  );
}