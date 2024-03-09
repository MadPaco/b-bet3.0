
interface DropdownProps {
    title: string;
    items: string[];
    isOpen: boolean;
    onToggle: () => void;
}

const Dropdown: React.FC<DropdownProps> = ({ title, items, isOpen, onToggle}) =>{

    return(
        <div className="text-gray-50">
            <div>
                <button onClick={onToggle}>{title}</button>
            </div>
            <div>
            { isOpen && (
                    <div className="origin-top-right absolute right-0 mt-2 rounded-md 
                    shadow-lg bg-gray-500 ring-1 ring-black ring-opacity-5
                    animate-fade-down animate-once animate-ease-linear">
                        <div className="mx-3 flex flex-col text-right">
                            {items.map((item, key) =>(
                                <a className="py-1" key={key} href="#">{item}</a>
                            ))}
                        </div>
                    </div>
                )}
            </div>
        </div>
    );
}

export default Dropdown;