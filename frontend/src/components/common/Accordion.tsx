interface AccordionProps {
  title: React.ReactNode;
  children: React.ReactNode;
  isOpen: boolean;
  toggleAccordion: () => void;
  currentWeek?: boolean;
}

const Accordion: React.FC<AccordionProps> = ({ title, children, isOpen, toggleAccordion, currentWeek }) => {

  return (
    <div className="border-b border-highlightCream text-white bg-gray-900 bg-opacity-90">
      <button
        className="w-full flex justify-center items-center py-2 px-4 text-center focus:outline-none"
        onClick={toggleAccordion}
      >
        <span className={`text-lg font-medium 
          ${currentWeek === true ? 'text-highlightGold' : ''} 
          ${isOpen ? 'animate-pulse' : ''}`}
        >{title}
        </span>
      </button>
      <div className="flex justify-center">
        <svg
          className={`w-5 h-5 transform transition-transform duration-300 ${isOpen ? 'rotate-180' : ''
            }`}
          fill="none"
          stroke="currentColor"
          viewBox="0 0 24 24"
          xmlns="http://www.w3.org/2000/svg"
        >
          <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7"></path>
        </svg>
      </div>

      <div className={`flex w-full justify-center overflow-hidden transition-max-height duration-500 bg-opacity-90 ${isOpen ? 'max-h-screen border-solid border-2 bg-gray-700 border-highlightCream' : 'max-h-0'}`}>
        <div className="py-2 px-4 w-full">
          {children}
        </div>
      </div>
    </div>
  );
};

export default Accordion;
