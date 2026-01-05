import './Textarea.css';

interface TextareaProps {
  label?: string;
  value: string;
  onChange: (value: string) => void;
  placeholder?: string;
  required?: boolean;
  className?: string;
  error?: string;
  disabled?: boolean;
  rows?: number;
}

export default function Textarea({
  label,
  value,
  onChange,
  placeholder,
  required = false,
  className = '',
  error,
  disabled = false,
  rows = 4
}: TextareaProps) {
  const handleChange = (e: React.ChangeEvent<HTMLTextAreaElement>) => {
    onChange(e.target.value);
  };

  return (
    <div className={`textarea-group ${className}`}>
      {label && (
        <label className="textarea-label">
          {label}
          {required && <span className="textarea-required">*</span>}
        </label>
      )}
      <textarea
        value={value}
        onChange={handleChange}
        placeholder={placeholder}
        required={required}
        disabled={disabled}
        rows={rows}
        className={`textarea ${error ? 'textarea--error' : ''}`}
      />
      {error && <span className="textarea-error">{error}</span>}
    </div>
  );
}
