interface FormButtonProps {
  buttonText: string;
}

const FormButton: React.FC<FormButtonProps> = ({ buttonText }) => {
  return (
    <button
      className="w-full px-3 py-2 bg-gray-500 text-white rounded"
      type="submit"
    >
      {buttonText}
    </button>
  );
};

export default FormButton;
