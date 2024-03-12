// FormInput.tsx
import { FontAwesomeIcon } from '@fortawesome/react-fontawesome';
import { faEye, faEyeSlash } from '@fortawesome/free-solid-svg-icons';

interface FormInputProps {
  placeholder: string;
  type: string;
  value: string;
  setValue: (value: string) => void;
  showPassword?: boolean;
  setShowPassword?: (value: boolean) => void;
}

const FormInput: React.FC<FormInputProps> = ({
  placeholder,
  type,
  value,
  setValue,
  showPassword,
  setShowPassword,
}) => {
  return (
    <div className="relative">
      <input
        className="w-full px-3 py-2 mb-4 border border-gray-300 rounded"
        placeholder={placeholder}
        type={type === 'password' && showPassword ? 'text' : type}
        value={value}
        onChange={(e) => setValue(e.target.value)}
      />
      {type === 'password' && (
        <div
          className="absolute inset-y-0 right-0 pr-3 pb-4 flex items-center space-x-1 cursor-pointer"
          onClick={() => setShowPassword && setShowPassword(!showPassword)}
        >
          <FontAwesomeIcon icon={showPassword ? faEyeSlash : faEye} />
          <div className="text-gray-400 text-sm">
            {showPassword ? 'Hide' : 'Show'}
          </div>
        </div>
      )}
    </div>
  );
};

export default FormInput;
