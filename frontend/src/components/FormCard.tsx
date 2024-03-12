// FormCard.tsx
interface FormCardProps {
  children: React.ReactNode;
}

const FormCard: React.FC<FormCardProps> = ({ children }) => {
  return (
    <div className="w-full max-w-xs mx-auto overflow-hidden bg-gray-900 bg-opacity-80 rounded-lg shadow-md dark:bg-gray-800">
      <div className="px-6 py-4">{children}</div>
    </div>
  );
};

export default FormCard;
