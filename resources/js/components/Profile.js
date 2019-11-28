import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import { Tabs, Tab, Form, Spinner, Col, Row, Modal, Card, Button} from 'react-bootstrap';
import axios from 'axios'
import moment from 'moment'
import SweetAlert from 'react-bootstrap-sweetalert';
import Alert from 'react-s-alert';

import ProfileBiodata from './ProfileBiodata'
import ProfilePassword from './ProfilePassword'

export default class Profile extends Component {
    constructor(props){
      super(props);

      this.state = {
        data: [],
        loading: false,
        id_personal: "",
        biodata: null,
        error: false,
        showFormAddBiodata: false
      }
    }

    componentDidMount(){
    }

    getBiodata(){
      this.setState({loading: true})

      let body = {id_personal: this.state.id_personal}
  
      axios.post(`/api/biodata`, body).then(response => {
        let result = response.data
        console.log(result.data)

        this.setState({
          biodata: result.data,
          loading: false
        })

        this.getPendidikan()
        
      }).catch(err => {
        console.log(err.response)

        this.setState({
          loading: false,
          error: true,
          errorMsg: err.response.data.message
        })
      })
    }

    render() {
        return (
          <div>
            <form>
              <Row style={{justifyContent: "center", display: this.state.loading ? "flex" : "none"}}>
                <Spinner style={{alignSelf: "center"}} animation="border" variant="primary" />
              </Row>

              <SweetAlert
                show={this.state.error}
                danger
                showCancel
                title="Maaf"
                btnSize="sm"
                confirmBtnBsStyle='success'
                cancelBtnText="Close"
                confirmBtnText="Buat data baru"
                onConfirm={() => this.setState({error: false, showFormAddBiodata: true})}
                onCancel={() => this.setState({error: false})}
              >{this.state.errorMsg}</SweetAlert>

              <Card>
                <Card.Header>Biodata</Card.Header>
                <Card.Body>
                  <ProfileBiodata data={this.state.biodata}/>
                </Card.Body>
              </Card>    

              <ProfilePassword data={this.state.biodata} visible={this.state.formEditPassword} onClose={() => this.setState({formEditPassword: false})}  />
              <Button variant="warning" className="btn-block" onClick={() => this.setState({formEditPassword: true})}>Ubah Password</Button>      
            </form>
            <Alert stack={{limit: 3}} position="top-right" offset="50" effect="slide" timeout={3000} />
          </div>
        );
    }
}

if (document.getElementById('profile')) {
    ReactDOM.render(<Profile />, document.getElementById('profile'));
}
