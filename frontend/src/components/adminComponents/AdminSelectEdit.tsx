import ButtonGroup from '../common/ButtonGroup';
import { useState } from 'react';

interface AdminSelectEditProps {
  onButtonSelect: (button: string) => void;
}

const AdminSelectEdit: React.FC<AdminSelectEditProps> = ({
  onButtonSelect,
}) => {
  const [active, setActive] = useState('edit');

  return (
    <div>
      <div className="">
        <div className="flex items-center justify-center">
          <button
            className="bg-green-400 rounded-lg px-3 mt-3"
            onClick={() => onButtonSelect('Submit Results')}
          >
            Enter results
          </button>
        </div>
        <ButtonGroup
          children={[
            <button onClick={() => setActive('edit')} name="edit" key={1}>
              Edit
            </button>,
            <button onClick={() => setActive('add')} name="add" key={2}>
              Add
            </button>,
          ]}
        />
        {active === 'edit' && (
          <ButtonGroup
            children={[
              <button onClick={() => onButtonSelect('Edit User')} key={1}>
                Edit User
              </button>,
              <button onClick={() => onButtonSelect('Edit Bet')} key={2}>
                Edit Bet
              </button>,
              <button onClick={() => onButtonSelect('Edit Game')} key={3}>
                Edit Game
              </button>,
            ]}
          />
        )}
        {active === 'add' && (
          <ButtonGroup
            children={[
              <button onClick={() => onButtonSelect('Add User')} key={1}>
                Add User
              </button>,
              <button onClick={() => onButtonSelect('Add Bet')} key={2}>
                Add Bet
              </button>,
              <button onClick={() => onButtonSelect('Add Game')} key={3}>
                Add Game
              </button>,
            ]}
          />
        )}
      </div>
      <div></div>
    </div>
  );
};

export default AdminSelectEdit;
