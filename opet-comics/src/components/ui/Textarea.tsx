import { TextareaHTMLAttributes } from 'react';

interface TextareaProps extends Omit<TextareaHTMLAttributes<HTMLTextAreaElement>, 'onChange'> {
  label?: string;
  value: string;
  onChange: (value: string) => void;
  error?: string;
  rows?: number;
}

export default function Textarea({
  label,
  value,
  onChange,
  error,
  required = false,
  rows = 4,
  className = '',
  disabled = false,
  ...props
}: TextareaProps) {

  const handleChange = (e: React.ChangeEvent<HTMLTextAreaElement>) => {
    onChange(e.target.value);
  };

  return (
    <div className={`space-y-2 ${className}`}>
      {label && (
        <label className="block text-sm font-semibold text-dark-700">
          {label}
          {required && <span className="text-danger-500 ml-1">*</span>}
        </label>
      )}
      <textarea
        value={value}
        onChange={handleChange}
        required={required}
        disabled={disabled}
        rows={rows}
        className={`
          w-full px-4 py-3 rounded-xl
          border-2 transition-all duration-300
          bg-white resize-vertical
          focus:outline-none focus:ring-2 focus:ring-offset-2
          disabled:opacity-50 disabled:cursor-not-allowed
          ${error
            ? 'border-danger-300 focus:border-danger-500 focus:ring-danger-500/20'
            : 'border-dark-200 focus:border-primary-500 focus:ring-primary-500/20 hover:border-dark-300'
          }
        `}
        {...props}
      />
      {error && (
        <p className="text-sm text-danger-600 font-medium animate-slide-down">
          {error}
        </p>
      )}
    </div>
  );
}
