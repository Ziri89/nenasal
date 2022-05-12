import {useContext} from 'react'
import {UserContext} from '../context/UserContext'

const Home = () => {
    const {user, logout, postinsert} = useContext(UserContext);
    return (
        <div className="home">
            <h1>{user.name}<br/><span>{user.email}</span></h1>
            <button onClick={logout} className="logout">Logout</button><br/>
            <button onClick={postinsert} className="postinsert">Publish Post</button>
        </div>
    )
}

export default Home;