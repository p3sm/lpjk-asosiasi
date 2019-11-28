import React, { Component } from 'react';
import { Form, Button, Row, Col, Card, Modal, Table, Spinner } from 'react-bootstrap';
import Datetime from 'react-datetime'
import InputMask from 'react-input-mask';
import MSelectProvinsi from './MSelectProvinsi'
import MSelectKabupaten from './MSelectKabupaten'
import axios from 'axios'
import Alert from 'react-s-alert';

// import { Container } from './styles';

export default class ProfileBiodata extends Component {
  constructor(props){
    super(props)

    this.state = {
      submiting: false,
      profile: null,
      loading: false,
      formEditBiodata: false,
      username: this.props.username
    }
  }

  componentDidMount(){
    this.getProfile();
  }

  handleClose = () => {
    this.setState({formEditBiodata: false})
  }

  getProfile(){
    this.setState({loading: true, profile: null})

    axios.get('/api/profile').then(response => {
      let result = response.data
      console.log(result.data)

      this.setState({
        profile: result.data,
        email: result.data.email,
        ktp: result.data.ktp,
        jabatan: result.data.jabatan,
        phone: result.data.phone,
        nama: result.data.nama,
        loading: false
      })
      
    }).catch(err => {
      console.log(err.response)

      this.setState({ loading: false })

      Alert.error(err.response.data.message);
    })
  }

  handleSubmit = () => {
    if(!/^[a-zA-Z0-9]+@[a-zA-Z0-9]+\.[A-Za-z]+$/.test(this.state.email)){
      Alert.error('Format email tidak valid')

      return
    }

    this.setState({submiting: true})

    var formData = new FormData();
    formData.append("email", this.state.email);
    formData.append("ktp", this.state.ktp);
    formData.append("jabatan", this.state.jabatan);
    formData.append("phone", this.state.phone);
    formData.append("nama", this.state.nama);

    axios.post(`/api/profile/edit`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    }).then(response => {
      console.log(response.data)
      
      this.setState({submiting: false, formEditBiodata: false})

      this.getProfile()

      Alert.success(response.data.message);
      
    }).catch(err => {
      console.log(err)

      this.setState({submiting: false})

      if(err.response){
        Alert.error(err.response.data.message);
      } else {
        Alert.error("An error occurred");
      }
    })
  }

  render() {
    return (
      <div>
        <Button className="mb-3" onClick={() => this.setState({formEditBiodata: true})}>Edit Profile</Button>
        {this.state.profile != null && (
          <Table bordered>
            <tbody>
              <tr>
                <th>Username</th>
                <td>{this.state.profile.user.name}</td>
                <th>Jabatan</th>
                <td>{this.state.profile.jabatan}</td>
              </tr>
              <tr>
                <th>Email</th>
                <td>{this.state.profile.email}</td>
                <th>Telepon</th>
                <td>{this.state.profile.phone}</td>
              </tr>
              <tr>
                <th>KTP</th>
                <td>{this.state.profile.ktp}</td>
                <th>Nama</th>
                <td>{this.state.profile.nama}</td>
              </tr>
            </tbody>
          </Table>
        )}
        <Modal
        size="xl"
        onHide={this.handleClose}
        show={this.state.formEditBiodata}>
          <Modal.Header closeButton>
            <Modal.Title>Edit Biodata</Modal.Title>
          </Modal.Header>
          
          <Row style={{justifyContent: "center", display: this.state.loading ? "flex" : "none"}}>
            <Spinner style={{alignSelf: "center"}} animation="border" variant="primary" />
          </Row>
          <Modal.Body>
          {this.state.profile != null && (
            <Form>
              <Row>
                <Col>
                  <Form.Group>
                    <Form.Label>Username</Form.Label>
                    <Form.Control disabled={true} placeholder="" value={this.state.profile.user.name}></Form.Control>
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Email</Form.Label>
                    <Form.Control type="email" id="email" name="email" onChange={(e) => this.setState({email: e.target.value})} placeholder="" value={this.state.email}></Form.Control>
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>KTP</Form.Label>
                    <Form.Control type="number" id="ktp" name="ktp" onChange={(e) => this.setState({ktp: e.target.value})} placeholder="" value={this.state.ktp}></Form.Control>
                  </Form.Group>
                </Col>
                <Col md>
                  <Form.Group>
                    <Form.Label>Jabatan</Form.Label>
                    <Form.Control type="text" id="jabatan" name="jabatan" onChange={(e) => this.setState({jabatan: e.target.value})} placeholder="" value={this.state.jabatan}></Form.Control>
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Telepon</Form.Label>
                    <Form.Control type="number" id="phone" name="phone" onChange={(e) => this.setState({phone: e.target.value})} placeholder="" value={this.state.phone}></Form.Control>
                  </Form.Group>
                  <Form.Group>
                    <Form.Label>Nama</Form.Label>
                    <Form.Control id="nama" name="nama" onChange={(e) => this.setState({nama: e.target.value})} placeholder="" value={this.state.nama}></Form.Control>
                  </Form.Group>
                </Col>
              </Row>
            </Form>
          )}
          </Modal.Body>
          <Modal.Footer>
            <Button variant="light" onClick={this.handleClose}>
              Cancel
            </Button>
            <Button className="d-flex" disabled={this.state.submiting} variant="primary" onClick={!this.state.submiting ? this.handleSubmit : null}>
              {this.state.submiting ? 'Submiting...' : 'Submit'}
            </Button>
          </Modal.Footer>
          <Alert stack={{limit: 3}} position="top-right" offset="50" effect="slide" timeout={3000} />
        </Modal>
      </div>
    );
  }
}
