import React from 'react';
import ReactDOM from 'react-dom/client';

// Core dynamic component: The Perfume Grid
const PerfumeGrid = () => {
  const [products, setProducts] = React.useState([]);
  const [loading, setLoading] = React.useState(true);

  React.useEffect(() => {
    const fetchProducts = async () => {
      try {
        let response = await fetch(`${daScientistGlobals.store_api_url}products`, {
          headers: {
            'X-WP-Nonce': daScientistGlobals.nonce,
          },
        });

        // Auto-renew expired security nonce on 403 Forbidden
        if (response.status === 403) {
          console.warn('REST Nonce expired. Requesting renewal token...');
          const refreshRes = await fetch('/wp-json/dascentist/v1/nonce');
          const data = await refreshRes.json();
          if (data.nonce) {
            daScientistGlobals.nonce = data.nonce;
            // Retry the request with the fresh nonce
            response = await fetch(`${daScientistGlobals.store_api_url}products`, {
              headers: {
                'X-WP-Nonce': daScientistGlobals.nonce,
              },
            });
          }
        }

        const data = await response.json();
        setProducts(data);
      } catch (err) {
        console.error('Failed to retrieve products:', err);
      } finally {
        setLoading(false);
      }
    };

    fetchProducts();
  }, []);

  if (loading) {
    return <div className="loading-state">Loading luxury molecular database...</div>;
  }

  return (
    <div className="perfume-grid">
      {products.map((product) => (
        <div key={product.id} className="product-card">
          <div className="product-image-wrap">
            {product.images && product.images[0] && (
              <img src={product.images[0].src} alt={product.images[0].name || product.name} />
            )}
          </div>
          <h3 className="product-title">{product.name}</h3>
          <div className="product-price" dangerouslySetInnerHTML={{ __html: product.price_html }} />
          <button className="add-to-cart-btn">Add to Cart</button>
        </div>
      ))}
    </div>
  );
};

// Mount dynamic elements on load
document.addEventListener('DOMContentLoaded', () => {
  const gridRoot = document.getElementById('perfume-grid-root');
  if (gridRoot) {
    ReactDOM.createRoot(gridRoot).render(
      <React.StrictMode>
        <PerfumeGrid />
      </React.StrictMode>
    );
  }
});
