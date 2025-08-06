import { useState } from 'react';
import { Button, Input } from '../ui';
import Modal from '../Modal/Modal';
import './PinProtection.css';

interface PinProtectionProps {
  isOpen: boolean;
  onClose: () => void;
  onSuccess: () => void;
  action: string; // "modifier" ou "supprimer"
  comicTitle?: string;
}

export default function PinProtection({ 
  isOpen, 
  onClose, 
  onSuccess, 
  action, 
  comicTitle 
}: PinProtectionProps) {
  const [pin, setPin] = useState('');
  const [error, setError] = useState('');
  const [isValidating, setIsValidating] = useState(false);

  const handleSubmit = async () => {
    if (!pin.trim()) {
      setError('Veuillez entrer un code PIN');
      return;
    }

    setIsValidating(true);
    setError('');

    try {
      // Appel au serveur pour vérifier le PIN
      const response = await fetch('/api/validate-pin.php', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ pin }),
      });

      const result = await response.json();

      if (result.success) {
        // Stocker l'autorisation temporairement (30 minutes)
        const expirationTime = Date.now() + (30 * 60 * 1000); // 30 minutes
        sessionStorage.setItem('admin_session', JSON.stringify({
          authorized: true,
          expires: expirationTime
        }));
        
        onSuccess();
        setPin('');
        setError('');
        onClose();
      } else {
        setError(result.message || 'Code PIN incorrect');
        setPin('');
      }
    } catch (err) {
      setError('Erreur de connexion. Veuillez réessayer.');
      console.error('Erreur validation PIN:', err);
    } finally {
      setIsValidating(false);
    }
  };

  const handleKeyPress = (e: React.KeyboardEvent) => {
    if (e.key === 'Enter') {
      handleSubmit();
    }
  };

  return (
    <Modal 
      isOpen={isOpen} 
      onClose={onClose} 
      title={`Autorisation requise`}
      variant="light"
    >
      <div className="pin-protection-content">
        <div className="pin-protection-info">
          <p><strong>Action :</strong> {action}</p>
          {comicTitle && <p><strong>Comic :</strong> {comicTitle}</p>}
          <p className="security-notice">
            🔒 Cette action nécessite une autorisation administrateur
          </p>
        </div>

        <div className="pin-input-section">
          <div onKeyPress={handleKeyPress}>
            <Input
              label="Code PIN Administrateur"
              type="password"
              value={pin}
              onChange={setPin}
              placeholder="Entrez votre code PIN"
              disabled={isValidating}
            />
          </div>
        </div>

        {error && (
          <div className="error-message">
            ⚠️ {error}
          </div>
        )}

        <div className="pin-protection-actions">
          <Button 
            onClick={handleSubmit}
            disabled={isValidating || !pin.trim()}
            variant="primary"
          >
            {isValidating ? 'Vérification...' : 'Valider'}
          </Button>
          <Button 
            variant="cancel" 
            onClick={onClose}
            disabled={isValidating}
          >
            Annuler
          </Button>
        </div>

        <div className="security-info">
          <small>
            💡 L'autorisation sera valide pendant 30 minutes
          </small>
        </div>
      </div>
    </Modal>
  );
}
