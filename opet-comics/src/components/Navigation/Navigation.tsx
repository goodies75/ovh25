import { Link, useLocation } from 'react-router-dom';
import { Palette, Home, Plus, Library } from 'lucide-react';
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
        <h1 style={{ display: 'flex', alignItems: 'center', gap: '10px' }}>
          <Palette size={28} />
          <span>Opet Comics</span>
        </h1>
        <p>Gérez votre collection de fiches comics</p>
      </div>
      
      <div className="nav-links">
        <Link
          to="/"
          className={`nav-link ${isActive('/') ? 'nav-link--active' : ''}`}
          style={{ display: 'flex', alignItems: 'center', gap: '8px' }}
        >
          <Home size={20} />
          <span>Accueil</span>
        </Link>
        <Link
          to="/add"
          className={`nav-link ${isActive('/add') ? 'nav-link--active' : ''}`}
          style={{ display: 'flex', alignItems: 'center', gap: '8px' }}
        >
          <Plus size={20} />
          <span>Ajouter</span>
        </Link>
        <Link
          to="/list"
          className={`nav-link ${isActive('/list') ? 'nav-link--active' : ''}`}
          style={{ display: 'flex', alignItems: 'center', gap: '8px' }}
        >
          <Library size={20} />
          <span>Liste</span>
        </Link>
      </div>
    </nav>
  );
}
