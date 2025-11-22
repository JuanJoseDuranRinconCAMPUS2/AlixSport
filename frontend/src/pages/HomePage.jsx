import fondoImage from "../assets/fondo.png"
import Navbar from "../components/Nabar";
import ProductCard from "../components/ProductCard";
import Footer from "../components/Footer";
import Cart from "../components/Cart";
import { useEffect, useState } from "react";
import axios from "axios";

const API = import.meta.env.VITE_API;

export default function HomePage({ 
  user, 
  onLoginClick, 
  onRegisterClick, 
  onLogout, 
  cart, 
  onAddToCart, 
  onRemoveFromCart, 
  onUpdateQuantity,
  onViewProduct,
  onAdminClick
}) {

  const [products, setProducts] = useState([]);
  const [isCartOpen, setIsCartOpen] = useState(false);

  const onCartClick = () => {
    if (!user) {
      onLoginClick()
    } else {
      setIsCartOpen(true);
    }
  };

  useEffect(() => {
    
    const getProducts = async () => {
      try {
        const { data } = await axios.get(`${API}/productos`);     
        setProducts(data);
        
      } catch (err) {
        console.log("Error cargando productos", err);
      }
    };

    getProducts();
  }, []);

  return (
    <div className="bg-neutral-900 text-white min-h-screen flex flex-col">
      <Navbar
        user={user}
        onLoginClick={onLoginClick}
        onRegisterClick={onRegisterClick}
        onLogout={onLogout}
        cartItemsCount={cart?.resumen?.productos_diferentes}
        onCartClick={onCartClick}
        onAdminClick={onAdminClick}
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
            <ProductCard 
              key={p.id_Producto} 
              product={p} 
              onAddToCart={() => onAddToCart(p, 1)}
              onViewProduct={() => onViewProduct(p)}
              user={user}
              onLoginClick={onLoginClick}
            />
          ))}
        </div>
      </div>

      <Cart
        isOpen={isCartOpen}
        onClose={() => setIsCartOpen(false)}
        cart={cart}
        onRemoveItem={onRemoveFromCart}
        onUpdateQuantity={onUpdateQuantity}
      />

      <Footer />
    </div>
  );
}