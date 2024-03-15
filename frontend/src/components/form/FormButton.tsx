interface FormButtonProps {
  buttonText: string;
  type: 'submit' | 'button';
  onClick?: () => void;
}

const FormButton: React.FC<FormButtonProps> = ({
  buttonText,
  type,
  onClick,
}) => {
  return (
    <button
      className="w-full px-3 py-2 bg-gray-500 text-white rounded"
      type={type}
      onClick={onClick}
    >
      {buttonText}
    </button>
  );
};

export default FormButton;
