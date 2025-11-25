export default function ProductCard({ product, onAddToCart, onViewProduct, user, onLoginClick }) {

  const imgSrc = `/src/assets/products/${product.imagen_Producto}`;
  
  return (
    <div className="bg-neutral-800 rounded-lg p-4 shadow hover:scale-105 transition">
      <div 
        className="cursor-pointer" 
        onClick={onViewProduct}
      >
        <img
          src={String(imgSrc)}
          alt={product.nombre_Producto}
          className="rounded-lg mb-3 w-full h-48 object-cover"
          onError={(e) => {
            e.target.onerror = null;
            e.target.src = "/src/assets/products/default.png";
          }}
        />
      </div>
      <h3 className="text-lg font-semibold">{product.nombre_Producto}</h3>
      <p className="text-lime-400 font-bold">${product.precio_Producto.toLocaleString()}</p>
      
      <div className="flex gap-2 mt-3">
        <button
          className="flex-1 bg-lime-500 text-black py-2 rounded-md hover:bg-lime-400 transition text-sm"
          onClick={(e) => {
            e.stopPropagation();
            if (!user) {
              onLoginClick()
            } else {
              onAddToCart()
            }
          }}
        >
          Agregar
        </button>
        <button
          className="flex-1 border border-lime-500 text-lime-500 py-2 rounded-md hover:bg-lime-500/10 transition text-sm"
          onClick={(e) => {
            e.stopPropagation();
            onViewProduct();
          }}
        >
          Ver Detalles
        </button>
      </div>
    </div>
  );
}