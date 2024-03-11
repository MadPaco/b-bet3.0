import { useCallback, useEffect, useRef } from 'react';

interface DropdownProps {
  title: string;
  items: string[];
  isOpen: boolean;
  onToggle: () => void;
}

const Dropdown: React.FC<DropdownProps> = ({
  title,
  items,
  isOpen,
  onToggle,
}) => {
  const node = useRef<HTMLDivElement>(null);

  const handleClickOutside = useCallback(
    (e: MouseEvent) => {
      if (node.current && !node.current.contains(e.target as Node)) {
        onToggle();
      }
    },
    [onToggle],
  );

  useEffect(() => {
    if (isOpen) {
      document.addEventListener('click', handleClickOutside);
    } else {
      document.removeEventListener('click', handleClickOutside);
    }

    return () => {
      document.removeEventListener('click', handleClickOutside);
    };
  }, [isOpen, handleClickOutside]);

  return (
    <div className="text-orange-500 relative">
      <button
        onClick={(e) => {
          e.stopPropagation();
          onToggle();
        }}
      >
        {title}
      </button>
      {isOpen && (
        <div
          ref={node}
          className="mt-1 origin-top-right absolute top-full rounded-md shadow-lg bg-gray-700 ring-1 ring-black ring-opacity-5 animate-fade-down animate-once animate-duration-150 animate-ease-linear"
        >
          <div className="mx-3 flex flex-col text-right">
            {items.map((item, key) => (
              <a className="py-1" key={key} href="#">
                {item}
              </a>
            ))}
          </div>
        </div>
      )}
    </div>
  );
};

export default Dropdown;
