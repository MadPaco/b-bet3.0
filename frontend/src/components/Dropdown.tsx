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
      document.addEventListener('mousedown', handleClickOutside);
    } else {
      document.removeEventListener('mousedown', handleClickOutside);
    }

    return () => {
      document.removeEventListener('mousedown', handleClickOutside);
    };
  }, [isOpen, handleClickOutside]);

  return (
    <div className="text-teal-500 relative">
      <button onClick={onToggle}>{title}</button>
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
