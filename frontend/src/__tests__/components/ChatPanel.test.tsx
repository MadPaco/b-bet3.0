// ChatPanel.test.tsx
import { render, screen, fireEvent } from '@testing-library/react';
import '@testing-library/jest-dom/extend-expect';
import ChatPanel from '../../components/layout/Panels/ChatPanel';

test('renders chat panel', () => {
  render(<ChatPanel color="blue" />);

  // Check that the chat panel is rendered
  const chatPanelElement = screen.getByTestId('chat-panel');
  expect(chatPanelElement).toBeInTheDocument();
});

test('sends message on button click', async () => {
  // Mock the fetch function to simulate a successful API call
  globalThis.fetch = jest.fn(() =>
    Promise.resolve(
      new Response(JSON.stringify({}), {
        status: 200,
        headers: {
          'Content-type': 'application/json',
        },
      }),
    ),
  );

  render(<ChatPanel color="blue" />);

  // Type a message into the input field
  const inputElement = screen.getByPlaceholderText('Type a message...');
  fireEvent.change(inputElement, { target: { value: 'Test message' } });

  // Click the send button
  const buttonElement = screen.getByText('Send');
  fireEvent.click(buttonElement);

  // Check that the fetch function was called with the right arguments
  expect(globalThis.fetch).toHaveBeenCalledWith(
    'http://127.0.0.1:8000/chatroom/1',
    expect.objectContaining({
      method: 'POST',
      body: JSON.stringify({ content: 'Test message' }),
    }),
  );
});
