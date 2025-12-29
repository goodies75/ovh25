import { Link, useLocation } from 'react-router-dom';
import { Home, Plus, BookOpen } from 'lucide-react';
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
        <div className="nav-brand-icon">
          <img src="/icones-comics.svg" alt="Comics d'Olivier" className="nav-logo" />
        </div>
        <div className="nav-brand-text">
          <h1>Comics d'Olivier</h1>
          <p>Site permettant de gérer ma collection de comics</p>
        </div>
      </div>

      <div className="nav-links">
        <Link
          to="/"
          className={`nav-link ${isActive('/') ? 'nav-link--active' : ''}`}
        >
          <Home size={18} /> Accueil
        </Link>
        <Link
          to="/add"
          className={`nav-link ${isActive('/add') ? 'nav-link--active' : ''}`}
        >
          <Plus size={18} /> Ajouter
        </Link>
        <Link
          to="/list"
          className={`nav-link ${isActive('/list') ? 'nav-link--active' : ''}`}
        >
          <BookOpen size={18} /> Collection
        </Link>
      </div>
    </nav>
  );
}
