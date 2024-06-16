import { useState } from 'react';

interface ReactionComponentProps {
  message: Message;
}

interface Reaction {
    id: number;
    reactionCode: string;
    user: string;
  }

interface Message {
    id: number;
    sender?: string;
    content: string;
    sentAt?: {
        date: string;
        timezone_type: number;
        timezone: string;
    };
    reactions?: Reaction[];
}


const ReactionComponent: React.FC<ReactionComponentProps> = ({ message }) => {
    const [reactions, setReactions] = useState(() => {
        const initialReactions: { [key: string]: number } = {};
      
        message.reactions?.forEach((reaction) => {
          initialReactions[reaction.reactionCode] = (initialReactions[reaction.reactionCode] || 0) + 1;
        });
      
        return initialReactions;
      });

      const reactionEmojis: { [key: string]: string } = {
        '1': '👍',
        // Add more mappings here as needed
      };
    const handleAddReaction = (reaction: number) => {
        const newCount = (reactions[reaction] || 0) + 1;

        fetch(`http://127.0.0.1:8000/api/chatroom/message/${message.id}/reaction`, {
        method: 'POST',
        headers: {
            'Authorization': `Bearer ${localStorage.getItem('token')}`,
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ reaction: reaction, count: newCount }),
        })
        .then((response) => {
            if (!response.ok) {
            throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then((data) => {
            const newReactions: { [key: string]: number } = {};
        
            data.forEach((reaction: Reaction) => {
            newReactions[reaction.reactionCode] = (newReactions[reaction.reactionCode] || 0) + 1;
            });
        
            setReactions(newReactions);
        })
        .catch((error) => {
            console.error('There has been a problem with your fetch operation:', error);
        });
    };

    return (
        <div>
            {Object.entries(reactions).map(([reactionCode, count]) => (
            <p key={reactionCode}>
                {reactionEmojis[reactionCode]}: {count}
            </p>
            ))}
        <button onClick={() => handleAddReaction(1)}>React 👍</button>
        </div>
    );
};

export default ReactionComponent;