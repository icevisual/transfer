var ProtoBuf = dcodeIO.ProtoBuf;
var ScentrealmBuilder = ProtoBuf.loadProtoFile("Scentrealm.proto");
var root = ScentrealmBuilder.build();
var Scentrealm = root.Proto2.Scentrealm;
var auth = new Scentrealm.AuthRequest();

auth.BaseRequest = new Scentrealm.BaseRequest(
		Scentrealm.SrSenderType.SST_controller, 1400255551, '33333333');
auth.Signature = 'sdfadfs';
auth.SignatureNonce = 'adsff';
auth.SignatureMethod = Scentrealm.SrSignatureMethod.SSM_hmac_sha1;

var builder = ProtoBuf.loadProtoFile("complex.proto"), Game = builder
		.build("Game"), Car = Game.Cars.Car;

var personBuilder = ProtoBuf.loadProtoFile("test/Person.pro");
var pRoot = personBuilder.build();
var p = new pRoot.tutorial.Person("name",12,"email",[new pRoot.tutorial.Person.PhoneNumber("12312331213",1)]);

var msgBook = new pRoot.tutorial.AddressBook([p]);

console.log('msgBook',msgBook);


// Construct with arguments list in field order:
var car = new Car("Rusty", new Car.Vendor("Iron Inc.", new Car.Vendor.Address(
		"US")), Car.Speed.SUPERFAST);

// OR: Construct with values from an object, implicit message creation (address) and enum values as strings:
var car = new Car({
	"model" : "Rusty",
	"vendor" : {
		"name" : "Iron Inc.",
		"address" : {
			"country" : "US"
		}
	},
	"speed" : "SUPERFAST" // also equivalent to "speed": 2
});

// OR: It's also possible to mix all of this!

// Afterwards, just encode your message:
var buffer = car.encode();