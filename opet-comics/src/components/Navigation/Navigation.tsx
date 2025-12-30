import { Link, useLocation } from 'react-router-dom';

export default function Navigation() {
  const location = useLocation();

  const isActive = (path: string) => {
    if (path === '/' && location.pathname === '/') return true;
    if (path !== '/' && location.pathname.startsWith(path)) return true;
    return false;
  };

  const links = [
    { path: '/', label: 'Accueil', icon: '🏠' },
    { path: '/add', label: 'Ajouter', icon: '➕' },
    { path: '/list', label: 'Liste', icon: '📚' },
  ];

  return (
    <nav className="sticky top-0 z-50 backdrop-blur-xl bg-white/70 border-b border-dark-100/50 shadow-lg mb-8">
      <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div className="flex items-center justify-between h-20">
          {/* Brand */}
          <Link to="/" className="group flex items-center space-x-3">
            <div className="text-3xl transform group-hover:scale-110 group-hover:rotate-12 transition-all duration-300">
              🎨
            </div>
            <div>
              <h1 className="text-2xl font-display font-bold bg-gradient-to-r from-primary-600 to-teal-600 bg-clip-text text-transparent">
                Opet Comics
              </h1>
              <p className="text-xs text-dark-500 hidden sm:block">
                Gérez votre collection
              </p>
            </div>
          </Link>

          {/* Links */}
          <div className="flex items-center space-x-2">
            {links.map((link) => (
              <Link
                key={link.path}
                to={link.path}
                className={`
                  relative px-4 py-2 sm:px-6 sm:py-3 rounded-xl font-medium
                  transition-all duration-300 transform hover:scale-105
                  flex items-center space-x-2
                  ${isActive(link.path)
                    ? 'bg-gradient-to-r from-primary-500 to-primary-600 text-white shadow-lg shadow-primary-500/30'
                    : 'text-dark-700 hover:bg-dark-100/50'
                  }
                `}
              >
                <span className="text-lg sm:text-xl">{link.icon}</span>
                <span className="hidden sm:inline">{link.label}</span>

                {/* Active indicator */}
                {isActive(link.path) && (
                  <div className="absolute -bottom-1 left-1/2 transform -translate-x-1/2 w-1/2 h-1 bg-white rounded-full animate-pulse"></div>
                )}
              </Link>
            ))}
          </div>
        </div>
      </div>
    </nav>
  );
}
