export type Game = {
    id: number;
    weekNumber: number;
    date: string;
    location: string;
    homeTeam: string;
    awayTeam: string;
    homeTeamLogo: string;
    awayTeamLogo: string;
    homeOdds: number;
    awayOdds: number;
    overUnder: number;
    homeScore: number | null;
    awayScore: number | null;
};

export type Bet = {
    id: number;
    gameID: number;
    username: string;
    homePrediction: number;
    awayPrediction: number;
    points: number;
};

export type User = {
    id: number;
    username: string;
    email: string;
    isAdmin: boolean;
    favTeam: string;
    profilePicture: string;
    bio: string;
    roles: string[];
};

export type TeamInfo = {
    id: number;
    name: string;
    division: string;
    conference: string;
    logo: string;
    primaryColor: string;
    location: string;
    losses: number;
    wins: number;
    ties: number;
    pointsFor: number;
    pointsAgainst: number;
    netPoints: number;
    shorthandName: string;
};

export type Result = {
    [gameID: string]: {
        homeTeamScore: number;
        awayTeamScore: number;
    };
}

