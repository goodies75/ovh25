import './Modal.css';
import { Button } from '../ui';

interface ModalProps {
  isOpen: boolean;
  onClose: () => void;
  title: string;
  children: React.ReactNode;
  actions?: React.ReactNode;
  variant?: 'dark' | 'light';
}

export default function Modal({ isOpen, onClose, title, children, actions, variant = 'dark' }: ModalProps) {
  if (!isOpen) return null;

  const handleOverlayClick = (e: React.MouseEvent) => {
    if (e.target === e.currentTarget) {
      onClose();
    }
  };

  return (
    <div className="modal-overlay" onClick={handleOverlayClick}>
      <div className={`modal-content ${variant === 'light' ? 'modal-content--light' : ''}`}>
        <div className="modal-header">
          <h3 className="modal-title">{title}</h3>
          <Button
            variant="delete"
            onClick={onClose}
            className="modal-close"
            title="Fermer"
          >
            ✕
          </Button>
        </div>
        
        <div className="modal-body">
          {children}
        </div>
        
        {actions && (
          <div className="modal-actions">
            {actions}
          </div>
        )}
      </div>
    </div>
  );
}
