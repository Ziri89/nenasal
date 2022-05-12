import {useContext, useState} from 'react'
import {Link} from 'react-router-dom'
import {UserContext} from '../context/UserContext';

const PostInsert = () => {
    const {postInsert, wait} = useContext(UserContext);
    const [errMsg, setErrMsg] = useState(false);
    const [successMsg, setSuccessMsg] = useState(false);
    const [formData, setFormData] = useState({
        user_id:'',
        category_id:'',
        tittle:'',
        body:''
    });

    const onChangeInput = (e) => {
        setFormData({
            ...formData,
            [e.target.name]:e.target.value
        })
    }

    const submitForm = async (e) => {
        e.preventDefault();

        if(!Object.values(formData).every(val => val.trim() !== '')){
            setSuccessMsg(false);
            setErrMsg('Please Fill in all Required Fields!');
            return;
        }

        const data = await postInsert(formData);
        if(data.success){
            e.target.reset();
            setSuccessMsg('You have successfully published post.');
            setErrMsg(false);
        }
        else if(!data.success && data.message){
            setSuccessMsg(false);
            setErrMsg(data.message);
        }
        
    }

    return (
        <div className="postform">
            <h2>Publish Post</h2>
            <form onSubmit={submitForm}>
                <label htmlFor="user_id">user_id:</label>
                <input type="text" name="user_id" onChange={onChangeInput} placeholder="Your user_id" id="name" value={formData.user_id} required />
                <label htmlFor="email">Email:</label>
                <input type="email" name="email" onChange={onChangeInput} placeholder="Your email" id="email" value={formData.email} required />
                <label htmlFor="password">Password:</label>
                <input type="password" name="password" onChange={onChangeInput} placeholder="Password" id="password" value={formData.password} required />
                {successMsg && <div className="success-msg">{successMsg}</div>}
                {errMsg && <div className="err-msg">{errMsg}</div>}
                <button type="submit" disabled={wait}>Publish Post</button>
            </form>
        </div>
    )
}

export default PostInsert;