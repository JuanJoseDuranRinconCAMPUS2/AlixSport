import { useState, useEffect } from "react";
import { ArrowLeft, ShoppingCart, Star, Truck, Shield } from "lucide-react";

export default function ProductDetail({ product, onBack, onAddToCart, user }) {
  const [selectedFlavor, setSelectedFlavor] = useState("");
  const [quantity, setQuantity] = useState(1);
  const [showSuccess, setShowSuccess] = useState(false);
  const imgSrc = `/src/assets/products/${product.imagen_Producto}`;

  const productFlavors = product.sabores?.map(s => s.nombre_Sabor) ?? ["Estándar"];

  const handleAddToCart = () => {
    onAddToCart(product.id_Producto, quantity);
    setShowSuccess(true);
    setTimeout(() => setShowSuccess(false), 3000);
  };

  const formatPrice = (price) => {
    return new Intl.NumberFormat('es-CO', {
      style: 'currency',
      currency: 'COP'
    }).format(price);
  };

  useEffect(() => {
    if (!selectedFlavor && productFlavors.length > 0) {
      setSelectedFlavor(productFlavors[0]);
    }
  }, [productFlavors]);

  return (
    <div className="min-h-screen bg-neutral-900 text-white">
      <header className="bg-neutral-800 py-4 px-6 flex items-center justify-between">
        <button
          onClick={onBack}
          className="flex items-center gap-2 text-lime-400 hover:text-lime-300 transition"
        >
          <ArrowLeft size={20} />
          Volver al catálogo
        </button>
        <h1 className="text-xl font-bold">ALIX SUPLEMENTOS</h1>
        <div className="w-20"></div>
      </header>

      <main className="container mx-auto px-6 py-8">
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-12">
          <div className="flex justify-center">
            <div className="bg-neutral-800 rounded-2xl p-8 max-w-md w-full">
              <img
                src={String(imgSrc)}
                alt={product.nombre_Producto}
                className="w-full h-80 object-cover rounded-xl shadow-2xl"
                onError={(e) => {
                  e.target.onerror = null;
                  e.target.src = "/src/assets/products/default.png";
                }}
              />
            </div>
          </div>

          <div className="space-y-6">
            <div>
              <h1 className="text-4xl font-bold mb-2">{product.nombre_Producto}</h1>
              <div className="flex items-center gap-2 mb-4">
                <div className="flex text-yellow-400">
                  {[1, 2, 3, 4, 5].map((star) => (
                    <Star key={star} size={20} fill="currentColor" />
                  ))}
                </div>
                <span className="text-neutral-400">(4.8/5 - 156 reseñas)</span>
              </div>
              <p className="text-3xl font-bold text-lime-500">
                {formatPrice(product.precio_Producto)}
              </p>
            </div>

            <div>
              <h3 className="text-lg font-semibold mb-2">Descripción</h3>
              <p className="text-neutral-300 leading-relaxed">
                {product.descripcion_Producto}
              </p>
            </div>

            <div>
              <h3 className="text-lg font-semibold mb-3">Sabor</h3>
              <div className="grid grid-cols-2 gap-3">
                {productFlavors.map((flavor) => (
                  <button
                    key={flavor}
                    onClick={() => setSelectedFlavor(flavor)}
                    className={`p-3 rounded-lg border-2 transition-all ${
                      selectedFlavor === flavor
                        ? "border-lime-500 bg-lime-500/10 text-lime-400"
                        : "border-neutral-600 hover:border-lime-400 text-neutral-300"
                    }`}
                  >
                    {flavor}
                  </button>
                ))}
              </div>
            </div>

            <div>
              <h3 className="text-lg font-semibold mb-3">Cantidad</h3>
              <div className="flex items-center gap-4">
                <div className="flex items-center gap-3 bg-neutral-800 rounded-lg p-2">
                  <button
                    onClick={() => setQuantity(Math.max(1, quantity - 1))}
                    className="w-8 h-8 flex items-center justify-center bg-neutral-700 rounded hover:bg-neutral-600 transition"
                  >
                    -
                  </button>
                  <span className="text-lg font-semibold w-8 text-center">
                    {quantity}
                  </span>
                  <button
                    onClick={() => setQuantity(quantity + 1)}
                    className="w-8 h-8 flex items-center justify-center bg-neutral-700 rounded hover:bg-neutral-600 transition"
                  >
                    +
                  </button>
                </div>
                <span className="text-neutral-400">
                  {quantity} unidad{quantity > 1 ? 'es' : ''}
                </span>
              </div>
            </div>

            <div className="space-y-4 pt-4">
              <button
                onClick={handleAddToCart}
                disabled={!user}
                className="w-full bg-lime-600 hover:bg-lime-500 disabled:bg-neutral-600 disabled:cursor-not-allowed text-black font-bold py-4 rounded-lg transition flex items-center justify-center gap-3"
              >
                <ShoppingCart size={24} />
                {user ? "Agregar al Carrito" : "Inicia sesión para comprar"}
              </button>

              {showSuccess && (
                <div className="bg-lime-600/20 border border-lime-500 text-lime-400 p-3 rounded-lg text-center">
                  ✅ Producto agregado al carrito exitosamente!
                </div>
              )}

              <button className="w-full border-2 border-lime-500 text-lime-500 hover:bg-lime-500/10 font-bold py-4 rounded-lg transition">
                Comprar ahora
              </button>
            </div>

            <div className="grid grid-cols-1 sm:grid-cols-3 gap-4 pt-6">
              <div className="flex items-center gap-3 text-neutral-300">
                <Truck size={20} className="text-lime-500" />
                <span className="text-sm">Envío gratis</span>
              </div>
              <div className="flex items-center gap-3 text-neutral-300">
                <Shield size={20} className="text-lime-500" />
                <span className="text-sm">Garantía 30 días</span>
              </div>
              <div className="flex items-center gap-3 text-neutral-300">
                <ShoppingCart size={20} className="text-lime-500" />
                <span className="text-sm">Devolución fácil</span>
              </div>
            </div>
          </div>
        </div>

        <section className="mt-16 bg-neutral-800 rounded-2xl p-8">
          <h2 className="text-2xl font-bold mb-6">Especificaciones Técnicas</h2>
          <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
              <h3 className="text-lg font-semibold mb-3">Información Nutricional</h3>
              <ul className="space-y-2 text-neutral-300">
                <li>• Proteína: 24g por porción</li>
                <li>• Carbohidratos: 3g por porción</li>
                <li>• Azúcares: 1g por porción</li>
                <li>• Grasas: 2g por porción</li>
                <li>• Calorías: 120 por porción</li>
              </ul>
            </div>
            <div>
              <h3 className="text-lg font-semibold mb-3">Modo de Uso</h3>
              <ul className="space-y-2 text-neutral-300">
                <li>• Mezclar 1 scoop con 250ml de agua</li>
                <li>• Consumir post-entreno</li>
                <li>• No exceder 2 porciones diarias</li>
                <li>• Mantener en lugar fresco y seco</li>
              </ul>
            </div>
          </div>
        </section>
      </main>
    </div>
  );
}