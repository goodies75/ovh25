import { HTMLAttributes } from 'react';

interface CardProps extends HTMLAttributes<HTMLDivElement> {
  children: React.ReactNode;
  hover?: boolean;
  glass?: boolean;
  gradient?: boolean;
}

export default function Card({
  children,
  className = '',
  hover = true,
  glass = false,
  gradient = false,
  onClick,
  ...props
}: CardProps) {

  const baseClasses = 'rounded-2xl transition-all duration-300';

  const backgroundClasses = glass
    ? 'bg-white/80 backdrop-blur-lg border border-white/20 shadow-glass'
    : gradient
    ? 'bg-gradient-to-br from-white to-primary-50/30 border border-primary-100/50 shadow-card'
    : 'bg-white border border-dark-100 shadow-card';

  const hoverClasses = hover
    ? glass
      ? 'hover:shadow-glass-hover hover:scale-[1.02] hover:border-white/40'
      : 'hover:shadow-card-hover hover:scale-[1.02] hover:border-primary-200'
    : '';

  const clickableClasses = onClick
    ? 'cursor-pointer active:scale-[0.98]'
    : '';

  const finalClassName = `${baseClasses} ${backgroundClasses} ${hoverClasses} ${clickableClasses} ${className}`.trim();

  return (
    <div
      className={finalClassName}
      onClick={onClick}
      {...props}
    >
      {children}
    </div>
  );
}
