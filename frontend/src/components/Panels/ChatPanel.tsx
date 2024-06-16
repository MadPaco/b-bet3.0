import { useState, useEffect, useRef } from 'react';
import { colorClasses } from '../../data/colorClasses';
import DOMPurify from 'dompurify';
import { useColor } from '../../context/ColorContext';
import ReactionComponent from '../chatComponents/ReactionComponent'

interface Message {
  id: number;
  sender?: string;
  content: string;
  sentAt?: {
    date: string;
    timezone_type: number;
    timezone: string;
  };
  reactions?: { [key: string]: number };
}

interface ChatPanelProps { }

const ChatPanel: React.FC<ChatPanelProps> = () => {
  const [messages, setMessages] = useState<Message[]>([]);
  const [newMessage, setNewMessage] = useState('');
  const chatBottom = useRef<null | HTMLDivElement>(null);
  const [isLoading, setIsLoading] = useState(true);

  const handleSendMessage = async () => {
    // filter out empty messages
    if (newMessage.trim() !== '') {
      const sanitizedMessage = DOMPurify.sanitize(newMessage);
      try {
        const response = await fetch(
          `http://127.0.0.1:8000/api/chatroom/?chatroomID=1`,
          {
            method: 'POST',
            headers: {
              'Content-Type': 'application/json',
              Authorization: `Bearer ${localStorage.getItem('token')}`,
            },
            body: JSON.stringify({ content: sanitizedMessage }),
          },
        );

        if (!response.ok) {
          throw new Error('HTTP error ' + response.status);
        }

        setNewMessage('');
        if (chatBottom.current) {
          setTimeout(() => {
            if (chatBottom.current) {
              chatBottom.current.scrollIntoView({ behavior: 'smooth' });
            }
          }, 100);
        }
      } catch (error) {
        console.error(error);
      }
    }
  };

  useEffect(() => {
    const fetchMessages = async () => {
      setIsLoading(true);
      try {
        const response = await fetch(
          'http://127.0.0.1:8000/api/chatroom/?chatroomID=1',
          {
            headers: {
              Authorization: `Bearer ${localStorage.getItem('token')}`,
            },
          },
        );

        if (!response.ok) {
          throw new Error('HTTP error ' + response.status);
        }

        const data = await response.json();

        // Map the fetched data to the Message structure
        const fetchedMessages: Message[] = data.map((message: Message) => {
          if (!message.sentAt) {
            console.log('Message does not have a sentAt property');
          }
          return {
            id: message.id,
            sender: message.sender,
            content: message.content,
            sentAt: message.sentAt.date.split('.')[0],
            reactions: message.reactions,
          };
        });

        setMessages(fetchedMessages);
        setIsLoading(false);
      } catch (error) {
        console.error(error);
      }
    };

    fetchMessages();

    // Subscribe to updates from the Mercure hub
    const url = new URL('http://localhost:3000/.well-known/mercure');
    url.searchParams.append('topic', '/chatroom/1'); // The topic to subscribe to
    const eventSource = new EventSource(url);

    eventSource.onmessage = (event) => {
      // When a new update is received, add the new message to the state
      const newMessage = JSON.parse(event.data);
      if (newMessage.sentAt) {
        newMessage.sentAt = newMessage.sentAt.date.split('.')[0];
      }
      setMessages((messages) => [...messages, newMessage]);
    };

    // Clean up the EventSource when the component is unmounted
    return () => {
      eventSource.close();
    };
  }, []);
  const { primaryColor } = useColor();
  const colorClass = primaryColor
    ? colorClasses[primaryColor as keyof typeof colorClasses]
    : 'bg-gray-400 hover:bg-gray-300';

  useEffect(() => {
    if (chatBottom.current) {
      chatBottom.current.scrollIntoView({ behavior: 'smooth' });
    }
  }, [messages]);

  return (
    <div className="p-5 m-6 cursor-pointer rounded-md backdrop-blur-sm text-white">
      <h2>Chat Panel</h2>
      <div className="overflow-auto h-64 mb-4 border-3 border-gray-900 bg-gray-700 rounded-md">
      {messages.map((message, index) => (
        <div key={index}>
          <p>
            <strong>
              {message.sender} (
              <span className="text-xs">
                {!isLoading && message.sentAt && !isNaN(new Date(message.sentAt).getTime()) 
                  ? new Intl.DateTimeFormat('us-US', {
                      month: 'short',
                      day: '2-digit',
                      hour: '2-digit',
                      minute: '2-digit',
                    }).format(new Date(message.sentAt))
                  : "Invalid date"}
              </span>
              ):
            </strong>{" "}
            {message.content}
          </p>
          <ReactionComponent message={message} />
        </div>
        ))}
        <div ref={chatBottom} />
      </div>
      <form
  onSubmit={(e) => {
    e.preventDefault();
    handleSendMessage();
  }}
  className="flex flex-col sm:flex-row"
>
  <input
    type="text"
    value={newMessage}
    onChange={(e) => setNewMessage(e.target.value)}
    className="border-2 border-gray-300 rounded-md p-2 flex-grow text-black sm:text-xs md:text-sm lg:text-base mb-2 sm:mb-0 flex-grow-0 sm:flex-grow"
  />
  <button
    type="submit"
    disabled={!newMessage}
    className={`ml-0 p-2 rounded-md ${colorClass} sm:ml-2 sm:mt-0 mt-2 flex-grow-0`}
  >
    Send
  </button>
</form>
    </div>
  );
};

export default ChatPanel;
