import { SelectHTMLAttributes } from 'react';

interface SelectOption {
  value: string;
  label: string;
}

interface SelectProps extends Omit<SelectHTMLAttributes<HTMLSelectElement>, 'onChange'> {
  label?: string;
  value: string;
  onChange: (value: string) => void;
  options: SelectOption[];
  placeholder?: string;
  error?: string;
}

export default function Select({
  label,
  value,
  onChange,
  options,
  error,
  required = false,
  placeholder,
  className = '',
  disabled = false,
  ...props
}: SelectProps) {

  const handleChange = (e: React.ChangeEvent<HTMLSelectElement>) => {
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
      <select
        value={value}
        onChange={handleChange}
        required={required}
        disabled={disabled}
        className={`
          w-full px-4 py-3 rounded-xl
          border-2 transition-all duration-300
          bg-white cursor-pointer
          focus:outline-none focus:ring-2 focus:ring-offset-2
          disabled:opacity-50 disabled:cursor-not-allowed
          ${error
            ? 'border-danger-300 focus:border-danger-500 focus:ring-danger-500/20'
            : 'border-dark-200 focus:border-primary-500 focus:ring-primary-500/20 hover:border-dark-300'
          }
        `}
        {...props}
      >
        {placeholder && (
          <option value="" disabled>
            {placeholder}
          </option>
        )}
        {options.map((option) => (
          <option key={option.value} value={option.value}>
            {option.label}
          </option>
        ))}
      </select>
      {error && (
        <p className="text-sm text-danger-600 font-medium animate-slide-down">
          {error}
        </p>
      )}
    </div>
  );
}
