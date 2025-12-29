// Composant de protection par PIN
import { useState } from 'react';
import { Button, Input, Modal } from './ui';

interface PinProtectionProps {
  isOpen: boolean;
  onClose: () => void;
  onSuccess: () => void;
  action: string; // "modifier" ou "supprimer"
}

export default function PinProtection({ isOpen, onClose, onSuccess, action }: PinProtectionProps) {
  const [pin, setPin] = useState('');
  const [error, setError] = useState('');
  
  // Votre code PIN secret (à changer !)
  const ADMIN_PIN = '1234';
  
  const handleSubmit = () => {
    if (pin === ADMIN_PIN) {
      onSuccess();
      setPin('');
      setError('');
      onClose();
    } else {
      setError('Code PIN incorrect');
      setPin('');
    }
  };

  return (
    <Modal isOpen={isOpen} onClose={onClose} title={`Code requis pour ${action}`} variant="light">
      <div style={{padding: '1rem'}}>
        <p>Entrez le code PIN administrateur :</p>
        <Input
          label="Code PIN"
          type="password"
          value={pin}
          onChange={setPin}
          placeholder="****"
        />
        {error && <p style={{color: 'red', marginTop: '0.5rem'}}>{error}</p>}
        <div style={{display: 'flex', gap: '1rem', marginTop: '1rem'}}>
          <Button onClick={handleSubmit}>Valider</Button>
          <Button variant="cancel" onClick={onClose}>Annuler</Button>
        </div>
      </div>
    </Modal>
  );
}
