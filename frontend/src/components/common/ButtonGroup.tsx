import { ReactElement } from 'react';
//take an array of buttons, pass an array with just 1 button for 1
interface ButtonGroupProps {
  children: ReactElement<'button'>[];
}

const ButtonGroup: React.FC<ButtonGroupProps> = ({ children }) => {
  const lastElementIndex = children.length - 1;
  const returnArray: JSX.Element[] = [];

  children.forEach((child, index) => {
    if (index === 0) {
      returnArray.push(
        <div className="py-1 px-2 rounded-l-lg bg-slate-500" key={index}>
          {child}
        </div>,
      );
    } else if (index === lastElementIndex) {
      returnArray.push(
        <div className="py-1 px-2 rounded-r-lg bg-slate-500" key={index}>
          {child}
        </div>,
      );
    } else {
      returnArray.push(
        <div className="py-1 px-2 bg-slate-500" key={index}>
          {child}
        </div>,
      );
    }
  });

  return <div className="flex justify-center text-lg m-3">{returnArray}</div>;
};

export default ButtonGroup;
