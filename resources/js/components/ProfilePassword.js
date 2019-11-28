import React, { Component } from 'react';
import { Form, Button, Row, Col, Card, Modal, Table, Spinner } from 'react-bootstrap';
import Datetime from 'react-datetime'
import InputMask from 'react-input-mask';
import MSelectProvinsi from './MSelectProvinsi'
import MSelectKabupaten from './MSelectKabupaten'
import axios from 'axios'
import Alert from 'react-s-alert';

// import { Container } from './styles';

export default class ProfilePassword extends Component {
  constructor(props){
    super(props)

    this.state = {
      submiting: false,
      password: "",
    }
  }

  componentDidMount(){
  }

  handleClose = () => {
    this.props.onClose()
  }

  handleSubmit = () => {
    if(this.state.password.length != 6){
      Alert.error('Password minimal 6 karakter')

      return
    }

    if(this.state.password != this.state.repassword){
      Alert.error('Konfirmasi password tidak sesuai')

      return
    }

    this.setState({submiting: true})

    var formData = new FormData();
    formData.append("password", this.state.password);

    axios.post(`/api/profile/changepassword`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    }).then(response => {
      console.log(response)
      
      this.setState({submiting: false})

      this.props.onSuccess()

      Alert.success(response.data.message);
      
    }).catch(err => {
      console.log(err.response.data.message)

      this.setState({submiting: false})
      Alert.error(err.response.data.message);
    })
  }

  render() {
    return (
      <Modal
      size="md"
      onHide={this.handleClose}
      show={this.props.visible}>
        <Modal.Header closeButton>
          <Modal.Title>Edit Password</Modal.Title>
        </Modal.Header>
        <Modal.Body>
          <Form>
            <Row>
              <Col>
                <Form.Group>
                  <Form.Label>Password</Form.Label>
                  <Form.Control type="password" id="password" name="password" onChange={(e) => this.setState({password: e.target.value})} placeholder="" value={this.state.password}></Form.Control>
                </Form.Group>
                <Form.Group>
                  <Form.Label>Konfirmasi Passowrd</Form.Label>
                  <Form.Control type="password" id="repassword" name="repassword" onChange={(e) => this.setState({repassword: e.target.value})} placeholder="" value={this.state.repassword}></Form.Control>
                </Form.Group>
              </Col>
            </Row>
          </Form>
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
    );
  }
}
