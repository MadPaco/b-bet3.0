import ButtonGroup from '../common/ButtonGroup';
import { useState } from 'react';

const AdminSelectEdit = () => {
  const [active, setActive] = useState('edit');

  return (
    <div>
      <div>
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
              <button key={1}>Edit User</button>,
              <button key={2}>Edit Bet</button>,
              <button key={3}>Edit Game</button>,
            ]}
          />
        )}
        {active === 'add' && (
          <ButtonGroup
            children={[
              <button key={1}>Add User</button>,
              <button key={2}>Add Bet</button>,
              <button key={3}>Add Game</button>,
            ]}
          />
        )}
      </div>
      <div></div>
    </div>
  );
};

export default AdminSelectEdit;
