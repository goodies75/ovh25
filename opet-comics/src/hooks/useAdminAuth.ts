import { useState, useEffect } from 'react';

interface AdminSession {
  authorized: boolean;
  expires: number;
}

export function useAdminAuth() {
  const [isAuthorized, setIsAuthorized] = useState(false);
  const [isLoading, setIsLoading] = useState(true);

  // Vérifier l'autorisation au chargement
  useEffect(() => {
    checkAuthorization();
  }, []);

  const checkAuthorization = () => {
    try {
      const sessionData = sessionStorage.getItem('admin_session');
      if (!sessionData) {
        setIsAuthorized(false);
        setIsLoading(false);
        return;
      }

      const session: AdminSession = JSON.parse(sessionData);
      const now = Date.now();

      if (session.authorized && session.expires > now) {
        setIsAuthorized(true);
      } else {
        // Session expirée
        sessionStorage.removeItem('admin_session');
        setIsAuthorized(false);
      }
    } catch (error) {
      console.error('Erreur lors de la vérification de l\'autorisation:', error);
      sessionStorage.removeItem('admin_session');
      setIsAuthorized(false);
    } finally {
      setIsLoading(false);
    }
  };

  const authorize = () => {
    setIsAuthorized(true);
  };

  const logout = () => {
    sessionStorage.removeItem('admin_session');
    setIsAuthorized(false);
  };

  // Vérification périodique de l'expiration
  useEffect(() => {
    if (!isAuthorized) return;

    const interval = setInterval(() => {
      checkAuthorization();
    }, 60000); // Vérifier toutes les minutes

    return () => clearInterval(interval);
  }, [isAuthorized]);

  return {
    isAuthorized,
    isLoading,
    authorize,
    logout,
    checkAuthorization
  };
}
