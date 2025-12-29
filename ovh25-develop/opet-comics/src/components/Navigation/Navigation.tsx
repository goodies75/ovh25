import { Link, useLocation } from 'react-router-dom';
import './Navigation.css';

export default function Navigation() {
  const location = useLocation();
  
  const isActive = (path: string) => {
    if (path === '/' && location.pathname === '/') return true;
    if (path !== '/' && location.pathname.startsWith(path)) return true;
    return false;
  };

  return (
    <nav className="navigation">
      <div className="nav-brand">
        <h1>🎨 Opet Comics</h1>
        <p>Gérez votre collection de fiches comics</p>
      </div>
      
      <div className="nav-links">
        <Link 
          to="/"
          className={`nav-link ${isActive('/') ? 'nav-link--active' : ''}`}
        >
          🏠 Accueil
        </Link>
        <Link 
          to="/add"
          className={`nav-link ${isActive('/add') ? 'nav-link--active' : ''}`}
        >
          ➕ Ajouter
        </Link>
        <Link 
          to="/list"
          className={`nav-link ${isActive('/list') ? 'nav-link--active' : ''}`}
        >
          📚 Liste
        </Link>
      </div>
    </nav>
  );
}
