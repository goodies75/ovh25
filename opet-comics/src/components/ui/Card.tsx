import './Card.css';

interface CardProps {
  children: React.ReactNode;
  className?: string;
  hover?: boolean;
  onClick?: () => void;
}

export default function Card({ children, className = '', hover = true, onClick }: CardProps) {
  const cardClass = `card ${hover ? 'card--hover' : ''} ${onClick ? 'card--clickable' : ''} ${className}`.trim();

  return (
    <div className={cardClass} onClick={onClick}>
      {children}
    </div>
  );
}
