import { useAdminAuth } from '../../hooks/useAdminAuth';
import { Button } from '../ui';
import './AdminStatus.css';

export default function AdminStatus() {
  const { isAuthorized, logout } = useAdminAuth();

  if (!isAuthorized) return null;

  return (
    <div className="admin-status">
      <div className="admin-status-content">
        <span className="admin-status-icon">🔓</span>
        <span className="admin-status-text">Mode Administrateur</span>
        <Button 
          variant="cancel" 
          onClick={logout}
          className="admin-logout-btn"
        >
          Déconnexion
        </Button>
      </div>
    </div>
  );
}
