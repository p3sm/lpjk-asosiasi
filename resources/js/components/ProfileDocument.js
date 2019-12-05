import React, { Component } from 'react';
import { Form, Button, Row, Col, Card, Modal, Table, Spinner } from 'react-bootstrap';
import Datetime from 'react-datetime'
import InputMask from 'react-input-mask';
import MSelectProvinsi from './MSelectProvinsi'
import MSelectKabupaten from './MSelectKabupaten'
import axios from 'axios'
import Alert from 'react-s-alert';

// import { Container } from './styles';

export default class ProfileDocument extends Component {
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
    this.getDocument();
  }

  handleClose = () => {
    this.setState({formEditBiodata: false})
  }

  getDocument(){
    this.setState({loading: true, profile: null})

    axios.get('/api/profile/file').then(response => {
      let result = response.data
      console.log(result.data)

      this.setState({
        profile: result.data,
        loading: false
      })
      
    }).catch(err => {
      console.log(err.response)

      this.setState({ loading: false })

      Alert.error(err.response.data.message);
    })
  }

  handleSubmit = () => {
    this.setState({submiting: true})

    var formData = new FormData();
    formData.append("file_ktp", this.state.file_ktp);
    formData.append("file_photo", this.state.file_photo);
    formData.append("file_pernyataan", this.state.file_pernyataan);

    axios.post(`/api/profile/uploadfile`, formData, {
      headers: {
        'Content-Type': 'multipart/form-data'
      }
    }).then(response => {
      console.log(response.data)
      
      this.setState({submiting: false, formEditBiodata: false})

      this.getDocument()

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

  onChangeHandler = event => {
    var size = event.target.files[0].size
    var label = $( event.target ).siblings("label")

    if(size > 20000000){
      Alert.error('Max file size 20mb')

      return
    }

    label.addClass("selected")
    label.html(event.target.files[0].name)

    switch(event.target.id){
      case "ktp":
        label.prepend("Upload KTP ")
        this.setState({ file_ktp: event.target.files[0] })
        break;
        case "foto":
          label.prepend("Upload Foto ")
          this.setState({ file_photo: event.target.files[0] })
          break;
      case "pernyataan":
        label.prepend("Upload Surat Pernyataan ")
        this.setState({ file_pernyataan: event.target.files[0] })
        break;
      default:
        break;
    }
  }

  render() {
    return (
      <div>
        <Button className="mb-3" onClick={() => this.setState({formEditBiodata: true})}>Edit Dokumen</Button>
        {!this.state.loading && (
          <Table bordered>
            <tbody>
              <tr>
                <th>Surat Pernyataan</th>
                <td><a data-type="iframe" data-fancybox target="_blank" href={this.state.profile ? this.state.profile.file_pernyataan: ""}>File</a></td>
                <th>Foto</th>
                <td><a data-type="iframe" data-fancybox target="_blank" href={this.state.profile ? this.state.profile.file_photo : ""}>File</a></td>
              </tr>
              <tr>
                <th>KTP</th>
                <td><a data-type="iframe" data-fancybox target="_blank" href={this.state.profile ? this.state.profile.file_ktp : ""}>File</a></td>
                <th>Template</th>
                <td>-</td>
              </tr>
            </tbody>
          </Table>
        )}
        <Modal
        size="xl"
        onHide={this.handleClose}
        show={this.state.formEditBiodata}>
          <Modal.Header closeButton>
            <Modal.Title>Edit Document</Modal.Title>
          </Modal.Header>
          
          <Row style={{justifyContent: "center", display: this.state.loading ? "flex" : "none"}}>
            <Spinner style={{alignSelf: "center"}} animation="border" variant="primary" />
          </Row>
          <Modal.Body>
          {!this.state.loading && (
            <Form>
              <Row>
                <Col md>
                  <div className="custom-file mb-3">
                    <input type="file" className="custom-file-input" id="ktp" onChange={this.onChangeHandler}></input>
                    <label className="custom-file-label" htmlFor="ktp">Upload KTP</label>
                  </div>
                  <div className="custom-file mb-3">
                    <input type="file" className="custom-file-input" id="foto" onChange={this.onChangeHandler}></input>
                    <label className="custom-file-label" htmlFor="foto">Upload Foto</label>
                  </div>
                  <div className="custom-file mb-3">
                    <input type="file" className="custom-file-input" id="pernyataan" onChange={this.onChangeHandler}></input>
                    <label className="custom-file-label" htmlFor="pernyataan">Upload Surat Pernyataan</label>
                  </div>
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
