import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios'
import moment from 'moment'

export default class User extends Component {
    constructor(props){
      super(props);

      this.state = {
        data: []
      }
    }

    componentDidMount(){
      axios.get(`/api/users`).then(response => {
        console.log(response.data)

        this.setState({
          data: response.data
        })
      }).catch(err => {
        console.log(err)
      })
    }

    render() {
        return (
          <div>
            <div>
                <a href="{{url('users/create')}}" className="btn bg-olive"><span>Add User</span></a>
            </div>
            <div>
                <table id="table-user" className="table table-striped table-bordered">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Name</th>
                            <th>Username</th>
                            <th>Role</th>
                            <th>Last Login</th>
                            <th>Active</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                  <tbody>
                  {this.state.data.map((d, i) => {
                    console.log(d)
                    return (
                      <tr key={i}>
                        <td>{i + 1}</td>
                        <td>{d.name}</td>
                        <td>{d.username}</td>
                        <td>{d.role_id}</td>
                        <td>{moment(d.last_login).format("DD MMMM YYYY, HH:mm:ss")}</td>
                        <td>{d.is_active == 1 ? "Active" : "Inactive"}</td>
                        <td>Edit</td>
                      </tr>
                    )
                  })}
                  </tbody>
                </table>
            </div>
          </div>
        );
    }
}

if (document.getElementById('user')) {
    ReactDOM.render(<User />, document.getElementById('user'));
}
